<?php
class Member
{
    private string $name, $role;
    private int $joined;

    public function __construct(string $name, string $role, int $joined)
    {
        $this->name = $name;
        $this->role = $role;
        $this->joined = $joined;
    }

    public function getName() {
        return $this->name;
    }

    public function getRole() {
        return $this->role;
    }

    public function getJoined() {
        return $this->joined;
    }
}