<?php

namespace Axi\MyCalendar\Exception;

class NoRendererFoundException extends \RuntimeException
{
    public function __construct(string $renderer)
    {
        parent::__construct('Couldn\'t find ' . $renderer . ' renderer');
    }
}
