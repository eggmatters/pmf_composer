<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace utilities\cache;

/**
 * Description of DBNode
 *
 * @author meggers
 */
class DBNode {
  private $tableName;
  private $columns;
  private $parents;
  private $children;
  public function __construct($tableName, $namespace, $columns = [], $parents = [], $children = []) {
    $this->tableName = $tableName;
    $this->namespace = $namespace;
    $this->columns = $columns;
    $this->parents = $parents;
    $this->children = $children;
  }
  public function getTableName() {
    return $this->tableName;
  }
  public function setTableName($tableName) {
    $this->tableName = $tableName;
    return $this;
  }
  public function getColumns() {
    return $this->columns;
  }
  public function setColumns($columns) {
    $this->columns = $columns;
    return $this;
  }
  public function getParents() {
    return $this->parents;
  }
  public function setParents($parents) {
    $this->parents = $parents;
    return $this;
  }
  public function getChildren() {
    return $this->children;
  }
  public function setChildren($children) {
    $this->children = $children;
    return $this;
  }
  public function getChild($childname) {
    return (isset($this->children[$childname])) ?
      $this->children[$childname] :
        null;            
  }
  public function setChild($key, DBNode &$child) {
    $this->children[$key] = $child;
    return $this;
  }
  public function getParent($parentName) {
    return (isset($this->parents[$parentName])) ?
      $this->parents[$parentName] :
        null;            
  }
  public function setParent($key, DBNode &$parent) {
    $this->parents[$key] = $parent;
    return $this;
  }
}
