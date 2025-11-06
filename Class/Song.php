<?php
declare(strict_types=1);

use Interface\SongInterface;

require_once __DIR__ . '/Interface/SongInterface.php';

final class Song implements SongInterface
{
    private string $title;
    private string $length; // "MM:SS"

    public function __construct(string $title, string $length)
    {
        $this->title  = $title;
        $this->length = $length;
    }

    public function getTitle(): string { return $this->title; }
    public function setTitle(string $title): void { $this->title = $title; }

    public function getLength(): string { return $this->length; }
    public function setLength(string $length): void { $this->length = $length; }

    public function toArray(): array
    {
        return [
            'title'  => $this->title,
            'length' => $this->length,
        ];
    }
}
