<?php

namespace Axi\MyCalendar;

use Axi\MyCalendar\Recipe\AbstractRecipe;
use Symfony\Component\Translation\TranslatableMessage;

class Event
{
    private TranslatableMessage $summary;
    private \DateTimeImmutable $dateTime;
    private string $sourceRecipe;

    public function __construct(\DateTimeImmutable $dateTime)
    {
        $this->dateTime = $dateTime;
    }

    public function getSummary(): TranslatableMessage
    {
        return $this->summary;
    }

    public function setSummary(TranslatableMessage $summary): void
    {
        $this->summary = $summary;
    }

    public function getDateTime(): \DateTimeImmutable
    {
        return $this->dateTime;
    }

    public function setDateTime(\DateTimeImmutable $dateTime): void
    {
        $this->dateTime = $dateTime;
    }

    public function getRelativeDaysFromNow(): int
    {
        $diff = (new \DateTime())->diff($this->dateTime);
        return ($diff->invert ? '-' : '') . $diff->days;
    }

    public function getAgeAt(\DateTimeInterface $birthDate): TranslatableMessage
    {
        $days = ($birthDate->diff($this->dateTime))->days;
        $nbYears = (int) ($days / AbstractRecipe::EARTH_DAYS_PER_YEAR);
        $nbMonths = (int) (($days - ($nbYears * AbstractRecipe::EARTH_DAYS_PER_YEAR)) / 30);

        return new TranslatableMessage('events.age_calc', ['nbYears' => $nbYears, 'nbMonths' => $nbMonths]);
    }

    public function setSourceRecipe(string $sourceRecipe): void
    {
        $this->sourceRecipe = $sourceRecipe;
    }

    public function getSourceRecipe(): string
    {
        return $this->sourceRecipe;
    }

    public function getDescription(): string
    {
        return '';
    }

    public function getUrl(): string
    {
        return '';
    }
}
