<?php
declare(strict_types=1);

use Interface\LinkInterface;

require_once __DIR__ . '/Interface/LinkInterface.php';

final class Link implements LinkInterface
{
    private ?string $website = null;
    private ?string $wikipedia = null;
    private ?string $spotify = null;
    private ?string $youtube = null;

    public function __construct(
        ?string $website = null,
        ?string $wikipedia = null,
        ?string $spotify = null,
        ?string $youtube = null
    ) {
        $this->website   = $website;
        $this->wikipedia = $wikipedia;
        $this->spotify   = $spotify;
        $this->youtube   = $youtube;
    }

    public function getWebsite(): ?string { return $this->website; }
    public function setWebsite(?string $website): void { $this->website = $website; }

    public function getWikipedia(): ?string { return $this->wikipedia; }
    public function setWikipedia(?string $wikipedia): void { $this->wikipedia = $wikipedia; }

    public function getSpotify(): ?string { return $this->spotify; }
    public function setSpotify(?string $spotify): void { $this->spotify = $spotify; }

    public function getYoutube(): ?string { return $this->youtube; }
    public function setYoutube(?string $youtube): void { $this->youtube = $youtube; }

    public function toArray(): array
    {
        return [
            'website'   => $this->website,
            'wikipedia' => $this->wikipedia,
            'spotify'   => $this->spotify,
            'youtube'   => $this->youtube,
        ];
    }
}
