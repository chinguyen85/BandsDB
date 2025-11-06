<?php
declare(strict_types=1);

namespace Interface;
interface MemberInterface
{
    public function getName(): string;

    public function setName(string $name): void;

    public function getRole(): string;

    public function setRole(string $role): void;

    public function getJoined(): int;

    public function setJoined(int $year): void;

    public function toArray(): array;
}
