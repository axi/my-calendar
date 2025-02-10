<?php

namespace Axi\Tests\Renderer;

use Axi\MyCalendar\Event;
use Axi\MyCalendar\Renderer\NoneRenderer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Translation\TranslatableMessage;

class NoneRendererTest extends TestCase
{
    private NoneRenderer $noneRenderer;

    public function setUp(): void
    {
        $this->noneRenderer = new NoneRenderer();
    }

    public function testRenderingNoEvents(): void
    {
        $rendered = $this->noneRenderer->render([], new \DateTimeImmutable());

        $this->assertIsArray($rendered);
        $this->assertEmpty($rendered);
    }


    public function testEventData(): void
    {
        $now = new \DateTimeImmutable();
        $event = new Event($now);
        /** @noinspection PhpTranslationKeyInspection */
        $event->setSummary(new TranslatableMessage('nope'));

        $rendered = $this->noneRenderer->render([$event], $now);

        $this->assertSame(
            $event,
            $rendered[0]
        );
    }
}
