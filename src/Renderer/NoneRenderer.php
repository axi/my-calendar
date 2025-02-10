<?php

namespace Axi\MyCalendar\Renderer;

use Axi\MyCalendar\Event;
use Symfony\Component\HttpFoundation\Response;

/** No rendering applied,  */
class NoneRenderer extends AbstractRenderer
{
    /**
     * @param Event[] $events
     * @return Event[] $events
     */
    public function render(array $events, \DateTimeInterface $baseDateTime): array|Response|string
    {
        return $events;
    }
}
