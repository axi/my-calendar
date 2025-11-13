<?php

namespace Axi\MyCalendar\Recipe;

use Symfony\Component\Translation\TranslatableMessage;

/**
 * Women's mean age at 1st childbirth in 2022 in the OECD
 */
class AverageAgeFirstChildrenRecipe extends DaysFromBirthDateRecipe
{
    public function __construct()
    {
        $d = 30.9; // years
        $this->daysFromBirth = (int) ($d * self::EARTH_DAYS_PER_YEAR);
    }

    public function getSummary(...$vars): TranslatableMessage
    {
        return new TranslatableMessage(
            'recipe.averageAgeFirstChildren'
        );
    }

    public function getName(): TranslatableMessage
    {
        return new TranslatableMessage(
            'recipe.averageAgeFirstChildren'
        );
    }

    public function getSource(): string
    {
        return 'https://www.oecd.org/en/publications/society-at-a-glance-2024_918d8db3-en.html';
    }
}
