<?php
class Photo
{
  private $id;
  public function __construct($id)
  {
    $this->id = $id;
  }

  public function __get($name)
  {
    if(isset($this->$name))
      return $this->$name;

    return null;
  }

  public function __set($name, $value)
  {
    $this->$name = $value;
  }
}
