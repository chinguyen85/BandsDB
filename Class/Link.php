<?php
class Link
{
    private string $website, $wikipedia, $spotify, $youtube;

    public function __construct(string $website, $wikipedia, $spotify, $youtube)
    {
        $this->website = $website;
        $this->wikipedia = $wikipedia;
        $this->spotify = $spotify;
        $this->youtube = $youtube;
    }

    public function setWebsite(string $website) {
        $this->website = $website;
    }
    public function getWebsite() {
        return $this->website;
    }

    public function setWikipedia(string $wikipedia) {
        $this->wikipedia = $wikipedia;
    }
    public function getWikipedia() {
        return $this->wikipedia;
    }

    public function setSpotify(string $spotify) {
        $this->spotify = $spotify;
    }
    public function getSpotify() {
        return $this->spotify;
    }

    public function setYoutube(string $youtube) {
        $this->youtube = $youtube;
    }
    public function getYoutube() {
        return $this->youtube;
    }
}