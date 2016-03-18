<?php
/**
 * Description of SimpleIterator
 *
 * @author meggers
 */
namespace core;

class SimpleIterator {
  private $index;
  private $collection;
  private $size;
  
  public function __construct($collection) {
    $this->collection = $collection;
    $this->index = 0;
    $this->current = null;
    $this->size = count($collection);
  }
  
  public function hasNext() {
    if ($this->index + 1 < $this->size) {
      return true;
    }
    return false;
  }
  
  public function hasPrevious() {
    if ($this->index - 1 >= 0) {
      return true;
    }
    return false;
  }
  
  public function current() {
    return $this->collection[$this->index];
  }
  
  public function next() {
    if ($this->hasNext()) {
      $this->index++;
      return $this->collection[$this->index];
    }
    return false;
  }
  
  public function previous() {
    if ($this->hasPrevious()) {
      $this->index--;
      return $this->collection[$this->index];
    }
    return false;
  }
  
  public function preparePrevious() {
    $this->index = count($this->collection) - 1;
  }
  
  public function getNext() {
    if ($this->hasNext()) {
      return $this->collection[$this->index + 1];
    }
    return false;
  }
  
  public function getPrevious() {
    if ($this->hasPrevious()) {
      return $this->collection[$this->index - 1];
    }
    return false;
  }
  
  public function getIndex() {
    return $this->index;
  }
  
  public function truncateFromIndex($index) {
    $collection = [];
    for ($i = $index; $i < count($this->collection); $i++) {
      $collection[] = $this->collection[$i];
    }
    return $collection;
  }
}
