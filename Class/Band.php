<?php
class Band
{
    private string $name, $origin;
    private int $funded;

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
    public function setAlbums(array $albums){
        $this->albums = $albums;
    }
    public function getAlbums(){
        return $this->albums;
    }
    public function setLinks(array $links){
        $this->links = $links;
    }
    public function getLinks(){
        return $this->links;
    }
    public function setMembers(array $members){
        $this->members = $members;
    }
    public function getMembers(){
        return $this->members;
    }

}