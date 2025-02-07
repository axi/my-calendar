<?php

namespace Axi\MyCalendar\Service;

use Axi\MyCalendar\Event;
use Axi\MyCalendar\Exception\NoRendererFoundException;
use Axi\MyCalendar\Recipe\RecipeInterface;
use Axi\MyCalendar\Renderer\RendererInterface;
use DateTimeImmutable;
use DateTimeInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\Response;
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

    public function getEventsFromDate(
        DateTimeInterface $dateTime,
        string $renderingFormat = 'none',
        string $locale = 'en'
    ): array|Response|string {
        $dateTime = DateTimeImmutable::createFromInterface($dateTime);

        // Check rendering first
        $found = false;
        $renderer = null;
        foreach ($this->getRenderers() as $renderer) {
            if ($renderer->getRendererFormat() === $renderingFormat) {
                $found = true;
                break;
            }
        }
        if (!$found) {
            throw new NoRendererFoundException($renderingFormat);
        }

        // Get events
        $events = [];
        foreach ($this->getRecipes() as $recipe) {
            if (in_array($renderingFormat, $recipe->getRenderingsAllowed(), true)) {
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

    private function getRecipes(): array
    {
        // Load recipes if not already loaded
        // If used with symfony bundle, recipes have already been injected
        if (null === $this->recipes) {
            $this->loadRecipes();
        }

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
            $this->loadRenderers();
        }

        return $this->renderers;
    }

    /**
     * Load all classes implementing RecipeInterface within the vendor
     */
    private function loadRecipes(): void
    {
        $this->recipes = $this->getClasses(
            dirname(__DIR__) . '/Recipe',
            'Axi\\MyCalendar\\Recipe\\',
            RecipeInterface::class
        );
    }

    private function loadRenderers(): void
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
}
