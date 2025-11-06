<?php

declare(strict_types=1);

namespace Interface;

interface BandInterface
{
    public function getName(): string;

    public function setName(string $name): void;

    public function getFounded(): int;

    public function setFounded(int $year): void;

    public function getOrigin(): string;

    public function setOrigin(string $origin): void;

    /** @return string[] */
    public function getGenres(): array;

    /** @param string[] $genres */
    public function setGenres(array $genres): void;

    public function addGenre(string $genre): void;

    public function removeGenre(string $genre): void;

    /** @return MemberInterface[] */
    public function getMembers(): array;

    /** @param MemberInterface[] $members */
    public function setMembers(array $members): void;

    public function addMember(MemberInterface $member): void;

    public function removeMemberByName(string $name): void;

    /** @return AlbumInterface[] */
    public function getAlbums(): array;

    /** @param AlbumInterface[] $albums */
    public function setAlbums(array $albums): void;

    public function addAlbum(AlbumInterface $album): void;

    public function removeAlbumByTitle(string $title): void;

    public function getLinks(): ?LinkInterface;

    public function setLinks(?LinkInterface $links): void;

    public function toArray(): array;
}
