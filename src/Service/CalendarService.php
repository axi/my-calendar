<?php

namespace Axi\MyCalendar\Service;

use Axi\MyCalendar\Event;
use Axi\MyCalendar\Exception\NoRendererFoundException;
use Axi\MyCalendar\Recipe\RecipeInterface;
use Axi\MyCalendar\Renderer\IcalRenderer;
use Axi\MyCalendar\Renderer\JsonRenderer;
use Axi\MyCalendar\Renderer\NoneRenderer;
use Axi\MyCalendar\Renderer\RendererInterface;
use DateTimeImmutable;
use DateTimeInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Yaml\Yaml;
use Throwable;

class CalendarService
{
    /**
     * @var RecipeInterface[] $recipes FQDN name indexed recipes
     */
    private ?array $recipes = null;

    /**
     * @var string[] $onlyRecipes List of FQDN class names to use (only those)
     */
    private array $onlyRecipes = [];

    /**
     * @var string[] $exceptRecipes List of FQDN class names to not use
     */
    private array $exceptRecipes = [];

    /**
     * @var RendererInterface[] $renderers FQDN name indexed renderers
     */
    private ?array $renderers = null;
    private bool $configuredRecipesRenderers = false;
    private ?array $renderingConfig = null;

    public function getEvents(
        DateTimeInterface $basedOnDate,
        string $renderingClass = NoneRenderer::class,
        string $locale = 'en'
    ): array|Response|string {
        $dateTime = DateTimeImmutable::createFromInterface($basedOnDate);

        // Check rendering first
        if (!array_key_exists($renderingClass, $this->getRenderers())) {
            throw new NoRendererFoundException($renderingClass);
        }
        $renderer = $this->getRenderers()[$renderingClass];

        // Get events
        $events = [];
        foreach ($this->getRecipes() as $recipe) {
            if (in_array($renderingClass, $recipe->getAllowedRenderings(), true)) {
                $events[] = $recipe->getEvents($dateTime);
            }
        }

        $events = array_merge([], ...$events);

        usort($events, static function (Event $a, Event $b) {
            return $a->getDateTime() > $b->getDateTime() ? 1 : -1;
        });

        $renderer->setLocale($locale);

        return $renderer->render($events, $dateTime);
    }

    /**
     * If not empty, will restrict the generation to those only recipes
     * @param string[] $onlyRecipes FQDN recipe class names
     */
    public function setOnlyRecipes(array $onlyRecipes): void
    {
        $this->onlyRecipes = $onlyRecipes;
    }

    /**
     * If not empty, will disable the generation of those recipes
     * @param string[] $exceptRecipes FQDN recipe class names
     */
    public function setExceptRecipes(array $exceptRecipes): void
    {
        $this->exceptRecipes = $exceptRecipes;
    }

    /**
     * Allow to inject a custom list of recipes.
     *
     * @param RecipeInterface[] $recipes FQDN recipe class name indexed recipes
     */
    public function setRecipes(array $recipes): void
    {
        foreach ($recipes as $recipe) {
            if (!in_array(RecipeInterface::class, class_implements($recipe), true)) {
                throw new \RuntimeException('Recipe ' . $recipe::class . ' must implements ' . RecipeInterface::class);
            }
        }
        $this->recipes = $recipes;
    }

    /**
     * Allow to inject a custom list of renderers.
     *
     * @param RendererInterface[] $renderers FQDN recipe class name indexed renderers
     */
    public function setRenderers(array $renderers): void
    {
        foreach ($renderers as $renderer) {
            if (!in_array(RendererInterface::class, class_implements($renderer), true)) {
                throw new \RuntimeException('Renderer ' . $renderer::class . ' must implements ' . RendererInterface::class);
            }
        }
        $this->renderers = $renderers;
    }

    private function getRecipes(): array
    {
        // Load recipes if not already loaded
        // If used with symfony bundle, recipes have already been injected
        if (null === $this->recipes) {
            $this->loadLocalRecipes();
        }
        $this->configureRecipesRenderers();

        if (!empty($this->onlyRecipes)) {
            $recipes = [];
            foreach ($this->onlyRecipes as $onlyRecipe) {
                if (array_key_exists($onlyRecipe, $this->recipes)) {
                    $recipes[] = $this->recipes[$onlyRecipe];
                }
            }

            return $recipes;
        }

        if (!empty($this->exceptRecipes)) {
            $recipes = $this->recipes;
            foreach ($this->exceptRecipes as $exceptRecipe) {
                if (array_key_exists($exceptRecipe, $recipes)) {
                    unset($recipes[$exceptRecipe]);
                }
            }

            return $recipes;
        }

        return $this->recipes;
    }

    private function getRenderers(): ?array
    {
        if (null === $this->renderers) {
            $this->loadLocalRenderers();
        }

        return $this->renderers;
    }

    /**
     * Load all classes implementing RecipeInterface within the vendor
     */
    private function loadLocalRecipes(): void
    {
        $this->recipes = $this->getClasses(
            dirname(__DIR__) . '/Recipe',
            'Axi\\MyCalendar\\Recipe\\',
            RecipeInterface::class
        );
    }

    private function loadLocalRenderers(): void
    {
        $this->renderers = $this->getClasses(
            dirname(__DIR__) . '/Renderer',
            'Axi\\MyCalendar\\Renderer\\',
            RendererInterface::class
        );
    }

    private function getClasses(string $path, string $namespace, string $implementingInterface): array
    {
        $classes = [];
        $finder = new Finder();
        $finder->files()->in($path)->name('*.php');
        foreach ($finder as $file) {
            $className = $namespace . $file->getFilenameWithoutExtension();
            if (class_exists($className)) {
                try {
                    $class = new $className();
                    if (in_array($implementingInterface, class_implements($class), true)) {
                        $classes[$className] = $class;
                    }
                } catch (Throwable) {
                    continue;
                }
            }
        }

        return $classes;
    }

    private function configureRecipesRenderers(): void
    {
        if (true === $this->configuredRecipesRenderers) {
            return;
        }
        $config = $this->getRecipeRenderingConfig();

        foreach ($this->recipes as $recipeClass => $recipe) {
            if (isset($config['only'][$recipeClass])) {
                $recipe->setAllowedRenderings($config['only'][$recipeClass]);
            } elseif (isset($config['exclude'][$recipeClass])) {
                $classes = [];
                foreach ($this->getAllRendererClasses() as $rendererClass) {
                    if (!in_array($rendererClass, $config['exclude'][$recipeClass], true)) {
                        $classes[] = $rendererClass;
                    }
                }
                $recipe->setAllowedRenderings($classes);
            } else {
                $recipe->setAllowedRenderings($this->getAllRendererClasses());
            }
        }

        $this->configuredRecipesRenderers = true;
    }

    private function getAllRendererClasses(): array
    {
        return array_keys($this->renderers);
    }

    private function getRecipeRenderingConfig(): array
    {
        if (!isset($this->renderingConfig)) {
            // If not loaded or provided, use local configuration
            $config = Yaml::parseFile(dirname(__DIR__, 2) . '/config/recipe_rendering.yaml');
            $this->renderingConfig = $config["axi_my_calendar"]["recipe_rendering"];
        }

        return $this->renderingConfig;
    }

    public function setRenderingConfig(?array $renderingConfig): void
    {
        $this->renderingConfig = $renderingConfig;
    }
}
