<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace core;

/**
 * Description of PMFReflectionClass
 *
 * @author matthewe
 */
class PMFReflectionClass extends \ReflectionClass {
  public function __construct($argument) {
    parent::__construct($argument);
  }
  
  public function getControllerMethods() {
    $methods = parent::getMethods(\ReflectionMethod::IS_PUBLIC);
    return array_filter($methods, function($currentMethod) {
      return($currentMethod->class == $this->name);
    });
  }
}
