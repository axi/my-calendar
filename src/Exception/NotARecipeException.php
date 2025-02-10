<?php

namespace Axi\MyCalendar\Exception;

use Axi\MyCalendar\Recipe\RecipeInterface;

class NotARecipeException extends AbstractRecipeException
{
    public function __construct(string $recipe)
    {
        parent::__construct('Recipe "' . $recipe . '" does not exists or must implements ' . RecipeInterface::class);
    }
}
