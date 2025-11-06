<?php
declare(strict_types=1);

use Interface\AlbumInterface;
use Interface\BandInterface;
use Interface\LinkInterface;
use Interface\MemberInterface;

require_once __DIR__ . '/Interface/BandInterface.php';
require_once __DIR__ . '/Interface/MemberInterface.php';
require_once __DIR__ . '/Interface/AlbumInterface.php';
require_once __DIR__ . '/Interface/LinkInterface.php';

final class Band implements BandInterface
{
    private string $name;
    private int $founded;
    private string $origin;
    /** @var string[] */
    private array $genres = [];
    /** @var MemberInterface[] */
    private array $members = [];
    /** @var AlbumInterface[] */
    private array $albums = [];
    private ?LinkInterface $links = null;

    public function __construct(
        string $name,
        int $founded,
        string $origin,
        array $genres = [],
        array $members = [],
        array $albums = [],
        ?LinkInterface $links = null
    ) {
        $this->name    = $name;
        $this->founded = $founded;
        $this->origin  = $origin;
        $this->setGenres($genres);
        $this->setMembers($members);
        $this->setAlbums($albums);
        $this->links = $links;
    }

    public function getName(): string { return $this->name; }
    public function setName(string $name): void { $this->name = $name; }

    public function getFounded(): int { return $this->founded; }
    public function setFounded(int $year): void { $this->founded = $year; }

    public function getOrigin(): string { return $this->origin; }
    public function setOrigin(string $origin): void { $this->origin = $origin; }

    /** @return string[] */
    public function getGenres(): array { return $this->genres; }

    /** @param string[] $genres */
    public function setGenres(array $genres): void
    {
        foreach ($genres as $g) {
            if (!is_string($g)) {
                throw new InvalidArgumentException('All genres must be strings');
            }
        }
        $this->genres = array_values($genres);
    }

    public function addGenre(string $genre): void
    {
        if (!in_array($genre, $this->genres, true)) {
            $this->genres[] = $genre;
        }
    }

    public function removeGenre(string $genre): void
    {
        $this->genres = array_values(array_filter(
            $this->genres,
            fn (string $g) => mb_strtolower($g) !== mb_strtolower($genre)
        ));
    }

    /** @return MemberInterface[] */
    public function getMembers(): array { return $this->members; }

    /** @param MemberInterface[] $members */
    public function setMembers(array $members): void
    {
        foreach ($members as $m) {
            if (!$m instanceof MemberInterface) {
                throw new InvalidArgumentException('All members must implement MemberInterface');
            }
        }
        $this->members = array_values($members);
    }

    public function addMember(MemberInterface $member): void
    {
        $this->members[] = $member;
    }

    public function removeMemberByName(string $name): void
    {
        $this->members = array_values(array_filter(
            $this->members,
            fn (MemberInterface $m) => mb_strtolower($m->getName()) !== mb_strtolower($name)
        ));
    }

    /** @return AlbumInterface[] */
    public function getAlbums(): array { return $this->albums; }

    /** @param AlbumInterface[] $albums */
    public function setAlbums(array $albums): void
    {
        foreach ($albums as $a) {
            if (!$a instanceof AlbumInterface) {
                throw new InvalidArgumentException('All albums must implement AlbumInterface');
            }
        }
        $this->albums = array_values($albums);
    }

    public function addAlbum(AlbumInterface $album): void
    {
        $this->albums[] = $album;
    }

    public function removeAlbumByTitle(string $title): void
    {
        $this->albums = array_values(array_filter(
            $this->albums,
            fn (AlbumInterface $a) => mb_strtolower($a->getTitle()) !== mb_strtolower($title)
        ));
    }

    public function getLinks(): ?LinkInterface { return $this->links; }
    public function setLinks(?LinkInterface $links): void { $this->links = $links; }

    public function toArray(): array
    {
        return [
            'name'    => $this->name,
            'founded' => $this->founded,
            'origin'  => $this->origin,
            'genres'  => $this->genres,
            'members' => array_map(fn ($m) => $m->toArray(), $this->members),
            'albums'  => array_map(fn ($a) => $a->toArray(), $this->albums),
            'links'   => $this->links ? $this->links->toArray() : null,
        ];
    }
}
