<?php

namespace Axi\MyCalendar\Exception;

use Axi\MyCalendar\Renderer\RendererInterface;

class NotARendererException extends AbstractRendererException
{
    public function __construct(string $renderer)
    {
        parent::__construct('Renderer "' . $renderer . '" does not exists or must implements ' . RendererInterface::class);
    }
}
