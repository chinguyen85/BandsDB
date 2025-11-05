<?php
class Band
{
    private string $name, $origin;
    private int $founded;

    private array $albums, $links, $members, $genres;

    public function __construct(string $name, string $origin, int $founded)
    {
        $this->name = $name;
        $this->origin = $origin;
        $this->founded = $founded;
    }

    public function getName(){
        return $this->name;
    }
    public function getOrigin(){
        return $this->origin;
    }
    public function getFounded(){
        return $this->founded;
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
    public function setGenres(array $genres){
        $this->genres = $genres;
    }
    public function getGenres(){
        return $this->genres;
    }
}