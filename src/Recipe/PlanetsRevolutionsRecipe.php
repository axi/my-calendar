<?php

namespace Axi\MyCalendar\Recipe;

use Axi\MyCalendar\Event;
use Symfony\Component\Translation\TranslatableMessage;

/**
 * list the dates where planets (other than earth) have made one or several revolutions
 */
class PlanetsRevolutionsRecipe extends AbstractRecipe
{
    /**
     * @var array|float[]
     */
    private array $revolutions;

    public function __construct()
    {
        $this->revolutions = [
            'recipe.planet.mercury' => 87.9691,
            'recipe.planet.venus' => 224.7,
            'recipe.planet.earth' => self::EARTH_DAYS_PER_YEAR,
            'recipe.planet.mars' => 687,
            'recipe.planet.jupiter' => 4331,
            'recipe.planet.saturn' =>  10747,
            'recipe.planet.uranus' => 30589,
            'recipe.planet.neptune' => 59800,
            'recipe.planet.pluto' => 90560,
        ];
    }

    public function getEvents(\DateTimeImmutable $basedOn): array
    {
        $maxYearsinDays = $this->maxYear * self::EARTH_DAYS_PER_YEAR;
        $allowedRevolutions = self::ALLOWED_ITERATIONS;

        $events = [];
        foreach ($this->revolutions as $object => $frequency) {
            $nbMaxRevolutions = (int) ($maxYearsinDays / $frequency);

            for (
                $iRevolutions = 1;
                $object !== 'recipe.planet.earth' && (
                    $iRevolutions === 1 || (
                        $iRevolutions <= $nbMaxRevolutions
                    )
                );
                $iRevolutions++
            ) {
                if (!in_array($iRevolutions, $allowedRevolutions, true)) {
                    continue;
                }
                $event = new Event(
                    (clone $basedOn)->add(new \DateInterval('P' . (int) ($iRevolutions * $frequency) . 'D'))
                );
                $event->setSummary($this->getSummary($iRevolutions, $object));
                $event->setSourceRecipe(self::class);
                $events[] = $event;
            }
        }

        return $events;
    }

    public function getSummary(...$vars): TranslatableMessage
    {
        return new TranslatableMessage(
            'recipe.planet-revolution',
            ['{object}' => new TranslatableMessage($vars[1]), '{count}' => $vars[0]]
        );
    }

    public function getSource(): string
    {
        return 'https://en.wikipedia.org/wiki/Orbital_period#Examples_of_sidereal_and_synodic_periods';
    }
}
