<?php
declare(strict_types=1);

use Interface\AlbumInterface;
use Interface\SongInterface;

require_once __DIR__ . '/Interface/AlbumInterface.php';
require_once __DIR__ . '/Interface/SongInterface.php';

final class Album implements AlbumInterface
{
    private string $title;
    private int $releaseYear;
    private string $genre;
    /** @var SongInterface[] */
    private array $songs = [];

    public function __construct(string $title, int $releaseYear, string $genre, array $songs = [])
    {
        $this->title       = $title;
        $this->releaseYear = $releaseYear;
        $this->genre       = $genre;
        $this->setSongs($songs);
    }

    public function getTitle(): string { return $this->title; }
    public function setTitle(string $title): void { $this->title = $title; }

    public function getReleaseYear(): int { return $this->releaseYear; }
    public function setReleaseYear(int $year): void { $this->releaseYear = $year; }

    public function getGenre(): string { return $this->genre; }
    public function setGenre(string $genre): void { $this->genre = $genre; }

    /** @return SongInterface[] */
    public function getSongs(): array { return $this->songs; }

    /** @param SongInterface[] $songs */
    public function setSongs(array $songs): void
    {
        foreach ($songs as $song) {
            if (!$song instanceof SongInterface) {
                throw new InvalidArgumentException('All songs must implement SongInterface');
            }
        }
        $this->songs = array_values($songs);
    }

    public function addSong(SongInterface $song): void
    {
        $this->songs[] = $song;
    }

    public function removeSongByTitle(string $title): void
    {
        $this->songs = array_values(array_filter(
            $this->songs,
            fn (SongInterface $s) => mb_strtolower($s->getTitle()) !== mb_strtolower($title)
        ));
    }

    public function toArray(): array
    {
        return [
            'title'        => $this->title,
            'release_year' => $this->releaseYear,
            'genre'        => $this->genre,
            'songs'        => array_map(fn ($s) => $s->toArray(), $this->songs),
        ];
    }
}
