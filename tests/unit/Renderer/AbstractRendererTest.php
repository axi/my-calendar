<?php

namespace Axi\Tests\Renderer;

use Axi\MyCalendar\Renderer\AbstractRenderer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\MessageCatalogueInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class AbstractRendererTest extends TestCase
{
    private AbstractRenderer $testedClass;

    public function setUp(): void
    {
        $this->testedClass = new class extends AbstractRenderer {
            public function render(array $events, \DateTimeInterface $baseDateTime): array|Response|string
            {
                // Implement render() method.
            }
        };
    }

    public function testSetLocale(): void
    {
        $this->testedClass->setLocale('de');
        $this->assertSame('de', $this->testedClass->getTranslator()->getLocale());
    }

    public function testTranslatorInitialized(): void
    {
        $this->assertInstanceOf(TranslatorInterface::class, $this->testedClass->getTranslator());
    }

    public function testTranslatorHasEnTranslations(): void
    {
        $this->testTranslatorHasTranslations('en', 'Now');
    }

    public function testTranslatorHasFrTranslations(): void
    {
        $this->testTranslatorHasTranslations('fr', 'Maintenant');
    }

    private function testTranslatorHasTranslations(string $locale, string $translation): void
    {
        $this->assertSame(
            $this->testedClass->getTranslator()->trans(
                'recipe.now',
                [],
                'messages' . MessageCatalogueInterface::INTL_DOMAIN_SUFFIX,
                $locale
            ),
            $translation
        );
    }
}
