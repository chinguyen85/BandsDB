<?php
class Song
{
 private string $title, $length;

 public function __construct(string $title, string $length)
 {
    $this->title = $title;
    $this->length = $length;
 }

 public function getTitle() {
    return $this->title;
 }

 public function getLength() {
    return $this->length;
 }
}