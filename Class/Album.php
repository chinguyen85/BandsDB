<?php
class Album
{
    private string $title, $genre;
    private int $releaseYear;
    private array $songs;

    public function __construct(string $title, int $releaseYear, string $genre)
    {
        $this->title = $title;
        $this->releaseYear = $releaseYear;
        $this->genre = $genre;
    }

    public function getTitle() {
        return $this->title;
    }

    public function getReleaseYear() {
        return $this->releaseYear;
    }

    public function getGenre() {
        return $this->genre;
    }

    public function setSongs(array $songs) {
        $this->songs = $songs;
    }

    public function getSongs() {
        return $this->songs;
    }
}