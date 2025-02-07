<?php

namespace Axi\MyCalendar\Renderer;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Translation\Loader\YamlFileLoader;
use Symfony\Component\Translation\MessageCatalogueInterface;
use Symfony\Component\Translation\Translator;

abstract class AbstractRenderer implements RendererInterface
{
    public const FORMAT = 'no-set';

    private ?Translator $translator = null;

    public function setLocale(string $locale): void
    {
        $this->getTranslator()->setLocale($locale);
    }

    public function getTranslator(): Translator
    {
        if (!isset($this->translator)) {
            $this->initTranslator();
        }

        return $this->translator;
    }

    public function setTranslator(Translator $translator): void
    {
        $this->translator = $translator;
    }

    private function initTranslator(): void
    {
        $this->translator = new Translator(class_exists(\Locale::class) ? \Locale::getDefault() : 'en');
        $this->translator->addLoader('icu', new YamlFileLoader());

        // Get and initialize translation files
        $finder = new Finder();
        $finder->files()->in(dirname(__DIR__, 2) . '/translations')->name('*.yaml');
        foreach ($finder as $file) {
            $locale = str_replace('messages+intl-icu.', '', $file->getFilenameWithoutExtension());
            $this->translator->addResource(
                'icu',
                $file->getRealPath(),
                $locale,
                'messages' . MessageCatalogueInterface::INTL_DOMAIN_SUFFIX
            );
        }

        // Set fallback
        $this->translator->setFallbackLocales(['en']);
    }
}
