<?php

namespace Axi\MyCalendar\Recipe;

use Axi\MyCalendar\Event;

abstract class DaysFromBirthDateRecipe extends AbstractRecipe
{
    protected int $daysFromBirth;

    public function getEvents(\DateTimeImmutable $basedOn): array
    {
        $event = new Event(
            (clone $basedOn)->add(new \DateInterval('P' . $this->daysFromBirth . 'D'))
        );
        $event->setSummary($this->getSummary());
        $event->setSourceRecipe(self::class);

        return [$event];
    }
}
