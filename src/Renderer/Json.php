<?php

namespace Axi\MyCalendar\Renderer;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class Json extends Renderer
{
    public function getRendererFormat(): string
    {
        return 'json';
    }

    public function render(array $events, \DateTimeInterface $baseDateTime): array|Response|string
    {
        $return = [];
        foreach ($events as $event) {
            $return[] = [
                'date' => $event->getDateTime()->format('Y-m-d'),
                'summary' => $event->getSummary()->trans($this->translator)
            ];
        }

        return new JsonResponse($return);
    }
}
