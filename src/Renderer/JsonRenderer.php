<?php

namespace Axi\MyCalendar\Renderer;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class JsonRenderer extends AbstractRenderer
{
    public function render(array $events, \DateTimeInterface $baseDateTime): array|Response|string
    {
        $return = [];
        foreach ($events as $event) {
            $return[] = [
                'date' => $event->getDateTime()->format('Y-m-d'),
                'summary' => $event->getSummary()->trans($this->getTranslator()),
                'daysFromNow' => $event->getRelativeDaysFromNow(),
                'ageAt' => $event->getAgeAt($baseDateTime)->trans($this->getTranslator()),
            ];
        }

        return new JsonResponse($return);
    }
}
