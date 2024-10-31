<?php

namespace Axi\MyCalendar\Exception;

class MissingVendorException extends \RuntimeException
{
    public function __construct(string $packageName)
    {
        parent::__construct(sprintf("Please install missing vendor '%s'", $packageName));
    }
}
