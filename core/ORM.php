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
  
  public function getAll($resources, $currentModel) {
    $queryBase = new QueryBase($currentModel);
    if (count($resources) == 1) {
      
    }
  }
}
