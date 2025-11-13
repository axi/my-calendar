<?php

namespace Axi\MyCalendar\Recipe;

use Symfony\Component\Translation\TranslatableMessage;

abstract class AbstractRecipe implements RecipeInterface
{
    private const MAX_YEAR_DEFAULT = 130;

    /**
     * @var int It makes no sense to continue listing events past the longest recorded human lifespan
     */
    protected int $maxYear = self::MAX_YEAR_DEFAULT;

    private ?array $allowedRenderings = null;

    // Sometimes, listing ALL the numbers makes no sense, but this suite of number does (for me at least)
    protected const ALLOWED_ITERATIONS = [1, 2, 3, 4, 5, 10, 15, 20, 25, 30, 40, 50, 100];

    public const EARTH_DAYS_PER_YEAR = 365.25636;

    /**
     * Some Recipe should not be use on specific renderings
     * @return string[]
     */
    public function getAllowedRenderings(): array
    {
        if (!isset($this->allowedRenderings)) {
            throw new \RuntimeException('Unconfigured allowed renderings');
        }

        return $this->allowedRenderings;
    }

    public function setAllowedRenderings(array $allowedRenderings): void
    {
        $this->allowedRenderings = $allowedRenderings;
    }

    public function getSource(): string
    {
        return self::class;
    }

    public function getName(): TranslatableMessage
    {
        return new TranslatableMessage($this::class);
    }
}
