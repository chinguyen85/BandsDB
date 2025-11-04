<?php
class band
{
    protected string $name, $origin;
    public int $funded;

    private array $albums, $links, $members;

    public function __construct(string $name, string $origin, int $funded)
    {
        $this->name = $name;
        $this->origin = $origin;
        $this->funded = $funded;
    }

    public function getName(){
        return $this->name;
    }

    public function getOrigin(){
        return $this->origin;
    }

}