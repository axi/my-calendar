<?php

namespace Axi\MyCalendar\Service;

use Axi\MyCalendar\Event;
use Axi\MyCalendar\Exception\NoRendererFoundException;
use Axi\MyCalendar\Recipe\Recipe;
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
     * @var Recipe[] $recipes
     */
    private array $recipes = [];

    /**
     * @var RendererInterface[] $recipes
     */
    private array $renderers = [];

    public function __construct()
    {
        $this->loadRecipes();
        $this->loadRenderers();
    }

    public function getEventsFromDate(
        DateTimeInterface $dateTime,
        string $renderingFormat = 'none',
        string $locale = 'en'
    ): array|Response|string {
        $dateTime = DateTimeImmutable::createFromInterface($dateTime);

        // Check rendering first
        $found = false;
        $renderer = null;
        foreach ($this->renderers as $renderer) {
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
        foreach ($this->recipes as $recipe) {
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
     * Load all classes implementing RecipeInterface
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
                        $classes[] = $class;
                    }
                } catch (Throwable) {
                    continue;
                }
            }
        }

        return $classes;
    }
}
