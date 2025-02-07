<?php

namespace Axi\MyCalendar\Recipe;

use Axi\MyCalendar\Event;
use Symfony\Component\Translation\TranslatableMessage;

interface RecipeInterface
{
    /** @return Event[] */
    public function getEvents(\DateTimeImmutable $basedOn): array;

    public function getSummary(...$vars): TranslatableMessage;

    public function getSource(): string;

    public function getRenderingsAllowed(): array;
}
