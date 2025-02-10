<?php

namespace Axi\Tests\Renderer;

use Axi\MyCalendar\Event;
use Axi\MyCalendar\Renderer\JsonRenderer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Translation\TranslatableMessage;

class JsonRendererTest extends TestCase
{
    private JsonRenderer $jsonRenderer;

    public function setUp(): void
    {
        $this->jsonRenderer = new JsonRenderer();
    }

    public function testRenderingNoEvents(): void
    {
        $rendered = $this->jsonRenderer->render([], new \DateTimeImmutable());

        $this->assertInstanceOf(
            JsonResponse::class,
            $rendered
        );

        $content = $rendered->getContent();
        $this->assertSame(
            $content,
            (new JsonResponse([]))->getContent()
        );

        $this->assertJson($content);
    }

    public function testRenderingOneEvents(): void
    {
        $this->testRenderingXEvents(1);
    }

    public function testEventData(): void
    {
        $now = new \DateTimeImmutable();
        $event = new Event($now);
        /** @noinspection PhpTranslationKeyInspection */
        $event->setSummary(new TranslatableMessage('nope'));

        $rendered = $this->jsonRenderer->render([$event], $now);
        $content = json_decode($rendered->getContent(), true, 512, JSON_THROW_ON_ERROR);

        $return = [
            'date' => $now->format('Y-m-d'),
            'summary' => 'nope',
            'daysFromNow' => 0,
            'ageAt' => "",
        ];

        $this->assertSame(
            $return,
            $content[0]
        );
    }

    public function testRenderingSeveralEvents(): void
    {
        $this->testRenderingXEvents(random_int(2, 30));
    }

    private function testRenderingXEvents(int $nb): void
    {
        $now = new \DateTimeImmutable();

        $events = [];
        for ($i = 1; $i <= $nb; $i++) {
            $event = new Event($now);
            /** @noinspection PhpTranslationKeyInspection */
            $event->setSummary(new TranslatableMessage('nope'));
            $events[] = $event;
        }

        $rendered = $this->jsonRenderer->render($events, $now);
        $this->assertInstanceOf(
            JsonResponse::class,
            $rendered
        );

        $content = $rendered->getContent();
        $this->assertJson($content);

        $d = json_decode($content, false, 512, JSON_THROW_ON_ERROR);
        $this->assertCount($nb, $d);
    }
}
