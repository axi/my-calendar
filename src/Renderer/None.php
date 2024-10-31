<?php

namespace Axi\MyCalendar\Renderer;

use Axi\MyCalendar\Event;
use Symfony\Component\HttpFoundation\Response;

/** No rendering applied,  */
class None extends Renderer
{
    public function getRendererFormat(): string
    {
        return 'none';
    }

    /**
     * @param Event[] $events
     * @return Event[] $events
     */
    public function render(array $events, \DateTimeInterface $baseDateTime): array|Response|string
    {
        return $events;
    }
}
