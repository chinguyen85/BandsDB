<?php
declare(strict_types=1);

use Interface\MemberInterface;

require_once __DIR__ . '/Interface/MemberInterface.php';

final class Member implements MemberInterface
{
    private string $name;
    private string $role;
    private int $joined;

    public function __construct(string $name, string $role, int $joined)
    {
        $this->name   = $name;
        $this->role   = $role;
        $this->joined = $joined;
    }

    public function getName(): string { return $this->name; }
    public function setName(string $name): void { $this->name = $name; }

    public function getRole(): string { return $this->role; }
    public function setRole(string $role): void { $this->role = $role; }

    public function getJoined(): int { return $this->joined; }
    public function setJoined(int $year): void { $this->joined = $year; }

    public function toArray(): array
    {
        return [
            'name'   => $this->name,
            'role'   => $this->role,
            'joined' => $this->joined,
        ];
    }
}
