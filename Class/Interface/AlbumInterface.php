<?php
declare(strict_types=1);

namespace Interface;

interface AlbumInterface
{
    public function getTitle(): string;

    public function setTitle(string $title): void;

    public function getReleaseYear(): int;

    public function setReleaseYear(int $year): void;

    public function getGenre(): string;

    public function setGenre(string $genre): void;

    /** @return SongInterface[] */
    public function getSongs(): array;

    /** @param SongInterface[] $songs */
    public function setSongs(array $songs): void;

    public function addSong(SongInterface $song): void;

    public function removeSongByTitle(string $title): void;

    public function toArray(): array;
}
