<?php

namespace Axi\MyCalendar\Renderer;

use Axi\MyCalendar\Event;
use Symfony\Component\HttpFoundation\Response;

interface RendererInterface
{
    public function getRendererFormat(): string;

    /**
     * @param Event[]            $events
     * @param \DateTimeInterface $baseDateTime
     * @return array|Response|string
     */
    public function render(array $events, \DateTimeInterface $baseDateTime): array|Response|string;

    public function setLocale(string $locale): void;
}
