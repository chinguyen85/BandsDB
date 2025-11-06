<?php
declare(strict_types=1);

namespace Interface;
interface LinkInterface
{
    public function getWebsite(): ?string;

    public function setWebsite(?string $website): void;

    public function getWikipedia(): ?string;

    public function setWikipedia(?string $wikipedia): void;

    public function getSpotify(): ?string;

    public function setSpotify(?string $spotify): void;

    public function getYoutube(): ?string;

    public function setYoutube(?string $youtube): void;

    public function toArray(): array;
}
