<?php

namespace Axi\MyCalendar\Recipe;

use Axi\MyCalendar\Event;
use Symfony\Component\Translation\TranslatableMessage;

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

    public function getName(): TranslatableMessage
    {
        return new TranslatableMessage('recipe.daysFromBirthDateName');
    }
}
