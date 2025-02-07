<?php

namespace Axi\MyCalendar\Recipe;

use Axi\MyCalendar\Event;
use Symfony\Component\Translation\TranslatableMessage;

class MillionMinutesRecipe extends AbstractRecipe
{
    private const ONE_MILLION = 10000000;

    public function getEvents(\DateTimeImmutable $basedOn): array
    {
        $events = [];
        $maxYearsinMinutes = $this->maxYear * self::EARTH_DAYS_PER_YEAR * 24 * 60;

        $i = 1;
        while ($i * self::ONE_MILLION < $maxYearsinMinutes) {
            $nbMinutes = $i * self::ONE_MILLION;
            $event = new Event(
                (clone $basedOn)->add(new \DateInterval('PT' . $nbMinutes . 'M'))
            );
            $event->setSummary($this->getSummary($nbMinutes));
            $event->setSourceRecipe(self::class);
            $events[] = $event;
            $i++;
        }

        return $events;
    }

    public function getSummary(...$vars): TranslatableMessage
    {
        return new TranslatableMessage(
            'recipe.minutes',
            [
                'nbMinutes' => $vars[0],
            ]
        );
    }

    public function getSource(): string
    {
        return '';
    }
}
