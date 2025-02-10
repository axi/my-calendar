<?php

namespace Axi\MyCalendar\Recipe;

use Axi\MyCalendar\Event;
use Symfony\Component\Translation\TranslatableMessage;

class BillionSecondsRecipe extends AbstractRecipe
{
    private const BILLION = 1000000000;

    public function getEvents(\DateTimeImmutable $basedOn): array
    {
        $maxYearsinSeconds = $this->maxYear * self::EARTH_DAYS_PER_YEAR * 24 * 60 * 60;

        $events = [];
        for ($i = 1; $i * self::BILLION <= $maxYearsinSeconds; $i++) {
            $nbSeconds = $i * self::BILLION;
            $event = new Event(
                (clone $basedOn)->add(new \DateInterval('PT' . $nbSeconds . 'S'))
            );
            $event->setSummary($this->getSummary($nbSeconds / self::BILLION));
            $event->setSourceRecipe(self::class);
            $events[] = $event;
        }

        return $events;
    }

    public function getSummary(...$vars): TranslatableMessage
    {
        return new TranslatableMessage('recipe.billionSeconds', ['nbBSeconds' => $vars[0]]);
    }
}
