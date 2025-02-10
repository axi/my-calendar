<?php

namespace Axi\MyCalendar\Exception;

class NoRecipeFoundException extends AbstractRecipeException
{
    public function __construct(string $recipe)
    {
        parent::__construct('Couldn\'t find ' . $recipe . ' recipe');
    }
}
