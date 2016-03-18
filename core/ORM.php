<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace core;

/**
 * Description of ORM
 *
 * @author meggers
 */
class ORM {
  
  static public function getAll($resources) {
    $resourcesIterator = new SimpleIterator($resources);
    $resourcesIterator->preparePrevious();
    $query = [];
    $query['base'] = "SELECT * FROM " . Inflector::tableize($resourcesIterator->current());
    while($resourcesIterator->hasPrevious()) {
      $resourceValue = $resourcesIterator->previous();
    }
  }
  
  static public function selectFrom($resource, $columns = null) {
    $columnsList = '*';
    if (!is_null($columns) && is_array($columns)) {
      $columnsList = implode(',', $columns);
    } else if (!is_null($columns) && is_string($columns)) {
      $columnsList = $columns;
    }
    return "SELECT $columnsList FROM " . Inflector::tableize($resource);
  } 
  
  static public function Join($query, $id, $resource) {
    $queryBase = [];
    if (is_string($query)) {
      $queryBase['base'] = $query;
    }
  }
}
