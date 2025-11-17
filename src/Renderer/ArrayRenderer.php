<?php

namespace Axi\MyCalendar\Renderer;

class ArrayRenderer extends AbstractRenderer
{
    public function render(array $events, \DateTimeInterface $baseDateTime): array
    {
        $return = [];
        foreach ($events as $event) {
            $return[] = [
                'date' => $event->getDateTime()->format('Y-m-d'),
                'summary' => $event->getSummary()->trans($this->getTranslator()),
                'daysFromNow' => $event->getRelativeDaysFromNow(),
                'ageAt' => $event->getAgeAt($baseDateTime)->trans($this->getTranslator()),
                'receipeName' => $event->getSourceRecipe(),
            ];
        }

        return $return;
    }
}
