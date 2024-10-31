<?php

namespace Axi\MyCalendar\Recipe;

use Axi\MyCalendar\Event;
use Symfony\Component\Translation\TranslatableMessage;

/**
 * Fancy dates where people reach multiple of thousand days
 */
class ThousandsDays extends Recipe
{
    public function getEvents(\DateTimeImmutable $basedOn): array
    {
        $basesList = self::ALLOWED_ITERATIONS;
        $maxYearsinDays = $this->maxYear * self::EARTH_DAYS_PER_YEAR;

        $events = [];
        foreach ($basesList as $base) {
            $nbDays = $base * 1000;
            if ($nbDays > $maxYearsinDays) {
                continue;
            }
            $event = new Event(
                (clone $basedOn)->add(new \DateInterval('P' . $nbDays . 'D'))
            );
            $event->setSummary($this->getSummary($base));
            $event->setSourceRecipe(self::class);
            $events[] = $event;
        }

        return $events;
    }

    public function getSummary(...$vars): TranslatableMessage
    {
        return new TranslatableMessage('recipe.thousands', ['{nbDays}' => $vars[0] * 1000]);
    }

    public function getSource(): string
    {
        return 'simple maths';
    }
}
