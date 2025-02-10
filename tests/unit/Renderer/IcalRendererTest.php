<?php

namespace Axi\Tests\Renderer;

use Axi\MyCalendar\Event;
use Axi\MyCalendar\Renderer\IcalRenderer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\TranslatableMessage;

class IcalRendererTest extends TestCase
{
    private IcalRenderer $icalRenderer;

    public function setUp(): void
    {
        $this->icalRenderer = new IcalRenderer();
    }

    public function testRenderingNoEvents(): void
    {
        $now = new \DateTimeImmutable();
        $renderedResponse = $this->icalRenderer->render([], $now);

        $this->assertInstanceOf(
            Response::class,
            $renderedResponse
        );

        $this->assertSame(
            'text/calendar; charset=utf-8',
            $renderedResponse->headers->get('Content-Type')
        );
        $this->assertSame(
            'inline; filename="my-calendar-' . $now->format('d-m-Y') . '.ics"',
            $renderedResponse->headers->get('Content-Disposition')
        );

        /**
         * BEGIN:VCALENDAR
         * PRODID:-//eluceo/ical//2.0/EN
         * VERSION:2.0
         * CALSCALE:GREGORIAN
         * END:VCALENDAR
         */
        $content = $renderedResponse->getContent();

        $this->assertStringStartsWith('BEGIN:VCALENDAR', $content);
        $this->assertStringEndsWith('END:VCALENDAR', trim($content));
    }

    public function testRenderingEvents(): void
    {
        $now = new \DateTimeImmutable();

        $event1 = new Event($now);
        /** @noinspection PhpTranslationKeyInspection */
        $event1->setSummary(new TranslatableMessage('nope'));

        $event2 = new Event($now);
        /** @noinspection PhpTranslationKeyInspection */
        $event2->setSummary(new TranslatableMessage('nope nope'));

        $events = [$event1, $event2];
        $renderedResponse = $this->icalRenderer->render($events, $now);

        /**
         * BEGIN:VCALENDAR
         * PRODID:-//eluceo/ical//2.0/EN
         * VERSION:2.0
         * CALSCALE:GREGORIAN
         * BEGIN:VEVENT
         * UID:7cf59e73a03ea699baa575e22f64d9dc
         * DTSTAMP:20250210T201439Z
         * SUMMARY:nope
         * DTSTART;VALUE=DATE:20250210
         * END:VEVENT
         * BEGIN:VEVENT
         * UID:80cdb40c95cfc8b1fdb8705d84101360
         * DTSTAMP:20250210T201439Z
         * SUMMARY:nope nope
         * DTSTART;VALUE=DATE:20250210
         * END:VEVENT
         * END:VCALENDAR
         */
        $content = $renderedResponse->getContent();

        $this->assertSame(count($events), substr_count($content, 'BEGIN:VEVENT'));
        $this->assertSame(count($events), substr_count($content, 'END:VEVENT'));
    }
}
