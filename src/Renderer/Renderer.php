<?php

namespace Axi\MyCalendar\Renderer;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Translation\Loader\YamlFileLoader;
use Symfony\Component\Translation\MessageCatalogueInterface;
use Symfony\Component\Translation\Translator;

abstract class Renderer implements RendererInterface
{
    protected Translator $translator;

    public function __construct()
    {
        // Init translator
        $this->translator = new Translator(class_exists(\Locale::class) ? \Locale::getDefault() : 'en');
        $this->translator->addLoader('icu', new YamlFileLoader());

        // Get and initialize translation files
        $finder = new Finder();
        $finder->files()->in(dirname(__DIR__, 2) . '/translations')->name('*.yaml');
        foreach ($finder as $file) {
            $locale = str_replace('messages+intl-icu.', '' , $file->getFilenameWithoutExtension());
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

    public function setLocale(string $locale): void
    {
        $this->translator->setLocale($locale);
    }
}
