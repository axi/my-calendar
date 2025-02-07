<?php

namespace Axi\MyCalendar\Recipe;

use Axi\MyCalendar\Event;
use Symfony\Component\Translation\TranslatableMessage;

/**
 * Estimated average sleep total time in years
 */
class SleepTimeRecipe extends AbstractRecipe
{
    public function getEvents(\DateTimeImmutable $basedOn): array
    {
        $allowed = self::ALLOWED_ITERATIONS;
        $events = [];
        foreach ($allowed as $nbYears) {
            $daysFromBirth = $this->getNbDaysFromBirth($nbYears);

            if ($daysFromBirth > $this->maxYear * self::EARTH_DAYS_PER_YEAR) {
                break;
            }

            $event = new Event(
                (clone $basedOn)->add(new \DateInterval('P' . $daysFromBirth . 'D'))
            );
            $event->setSummary($this->getSummary($nbYears));
            $event->setSourceRecipe(self::class);
            $events[] = $event;
        }

        return $events;
    }

    public function getSummary(...$vars): TranslatableMessage
    {
        return new TranslatableMessage(
            'recipe.sleep-time',
            ['{nbYears}' => $vars[0]]
        );
    }

    public function getSource(): string
    {
        return 'https://douglas.research.mcgill.ca/fr/sommeil-et-enfant-donnees-scientifiques/';
    }

    /** @todo: improve by finding a polynomial function in scientific literacy */
    private function getNbDaysFromBirth(int $nbYears): int
    {
        $totalHoursInXYears = (int) ($nbYears * self::EARTH_DAYS_PER_YEAR * 24);
        $sleepByAgesInMonths = [
            0 => 15.5, // 0-3 month, 14-17 h
            4 => 13.5,  //  4-11 month, 12-15 h
            12 => 12.5, // 1-2 year, 11-14h
            (3 * 12) => 11.5, // 3-5 year, 10-13h
            (6 * 12) => 10, //  6-13 year, 9-11h
            (14 * 12) => 9, //  14-17 year, 8-10h adolescence
            (18 * 12) => 8, //  18-25 year, 7-9h
            (26 * 12) => 8, //  26-64 year
        ];

        $sumHoursSleeping = 0;
        $lastExistingMonth = null;
        for ($day = 1; $sumHoursSleeping < $totalHoursInXYears; $day++) {
            $month = (int) ($day / (self::EARTH_DAYS_PER_YEAR / 12));
            if (isset($sleepByAgesInMonths[$month])) {
                $lastExistingMonth = $month;
                $add = $sleepByAgesInMonths[$month];
            } else {
                // While there's no new value (for $month = 1 per ex, use previous value)
                $add = $sleepByAgesInMonths[$lastExistingMonth];
            }
            $sumHoursSleeping += $add;
        }

        return ($day - 1);
    }
}
