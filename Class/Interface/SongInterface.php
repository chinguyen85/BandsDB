<?php
declare(strict_types=1);

namespace Interface;
interface SongInterface
{
    public function getTitle(): string;

    public function setTitle(string $title): void;

    public function getLength(): string; // e.g. "05:12"

    public function setLength(string $length): void;

    public function toArray(): array;
}
