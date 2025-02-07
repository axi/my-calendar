<?php

namespace Axi\MyCalendar\Renderer;

use Axi\MyCalendar\Event as MyCalendarEvent;
use Axi\MyCalendar\Exception\MissingVendorException;
use Composer\InstalledVersions;
use Eluceo\iCal\Domain\Entity\Calendar;
use Eluceo\iCal\Domain\Entity\Event;
use Eluceo\iCal\Domain\ValueObject\Date;
use Eluceo\iCal\Domain\ValueObject\SingleDay;
use Eluceo\iCal\Domain\ValueObject\UniqueIdentifier;
use Eluceo\iCal\Domain\ValueObject\Uri;
use Eluceo\iCal\Presentation\Factory\CalendarFactory;
use Symfony\Component\HttpFoundation\Response;

class Ical extends Renderer
{
    public function getRendererFormat(): string
    {
        return 'ical';
    }

    /**
     * @param MyCalendarEvent[]  $events
     * @param \DateTimeInterface $baseDateTime
     * @return array|\Symfony\Component\HttpFoundation\Response|string
     */
    public function render(array $events, \DateTimeInterface $baseDateTime): array|Response|string
    {
        $this->checkVendor();

        $icalEvents = [];
        foreach ($events as $event) {
            $uuid = md5(self::class . $event->getSummary() . $event->getDateTime()->format("dmY"));
            $ev = (new Event(new UniqueIdentifier($uuid)))
                ->setSummary($event->getSummary()->trans($this->getTranslator()))
                ->setOccurrence(new SingleDay(new Date($event->getDateTime())));

            if (!empty($event->getDescription())) {
                $ev->setDescription($event->getDescription());
            }
            if (!empty($event->getUrl())) {
                $uri = new Uri($event->getUrl());
                $ev->setUrl($uri);
            }

            $icalEvents[] = $ev;
        }

        // 2. Create Calendar domain entity
        $calendar = new Calendar($icalEvents);

        // 3. Transform domain entity into an iCalendar component
        $componentFactory = new CalendarFactory();
        $calendarComponent = $componentFactory->createCalendar($calendar);

        $response = new Response();

        // 4. Set headers
        $response->headers->set('Content-Type', 'text/calendar; charset=utf-8');
        $response->headers->set(
            'Content-Disposition',
            'inline; filename="my-calendar-' . $baseDateTime->format('d-m-Y'). '.ics"'
        );
        $response->setContent($calendarComponent);

        return $response;
    }

    private function checkVendor(): void
    {
        if (!InstalledVersions::isInstalled('eluceo/ical')) {
            throw new MissingVendorException('eluceo/ical');
        }
    }
}
