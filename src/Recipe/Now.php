<?php

namespace Axi\MyCalendar\Recipe;

use Axi\MyCalendar\Event;
use Symfony\Component\Translation\TranslatableMessage;

/**
 * Special recipe to dispaly the current day within the date list
 */
class Now extends Recipe
{
    public function getEvents(\DateTimeImmutable $basedOn): array
    {
        $event = new Event(
            new \DateTimeImmutable()
        );
        $event->setSummary($this->getSummary());
        $event->setSourceRecipe(self::class);

        return [$event];
    }

    public function getSummary(...$vars): TranslatableMessage
    {
        return new TranslatableMessage(
            'recipe.now'
        );
    }

    public function getSource(): string
    {
        return '';
    }

    public function getRenderingsAllowed(): array
    {
        return ['html', 'none'];
    }
}
