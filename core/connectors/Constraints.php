<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace core\connectors;

use core\resolver\Inflector;
/**
 * Description of Constraints
 *
 * @author meggers
 */
class Constraints {
  private $constraints;
  private $bindings;
  
  public function __construct() {
    $this->constraints = "";
    $this->bindings = [];
  }
  
  public function term($lhs, $operator, $rhs, $bind = true) {
    $this->constraints .= $this->setTerm($lhs, $operator, $rhs, $bind);
    return $this;
  }
  
  public function andTerm($lhs = "", $operator = "", $rhs = "", $bind = true) {
    $this->constraints .= "AND " . $this->setTerm($lhs, $operator, $rhs, $bind);
    return $this;
  }
  
  public function orTerm($lhs = "", $operator = "", $rhs = "", $bind = true) {
    $this->constraints .= "OR "  . $this->setTerm($lhs, $operator, $rhs, $bind);
    return $this;
  }
  
  public function groupBegin() {
    $this->constraints .= "( ";
    return $this;
  }
  
  public function groupEnd() {
    $this->constraints .= " ) ";
    return $this;
  }
  
  public function getConstraints() {
    return $this->constraints;
  }
  
  public function getBindings() {
    return $this->bindings;
  }
  
  private function setTerm($lhs = "", $operator = "", $rhs = "", $bind = true) {
    if ((!empty($lhs) && !empty($rhs)) && $bind) {
      $rhs = $this->setBindings($lhs, $rhs);
    }
    return trim("$lhs $operator $rhs") . " ";
  }
  
  private function setBindings($lhs, $rhs) {
    $placeholder = ":" . Inflector::underscore($lhs);
    $this->bindings[$placeholder] = $rhs;
    return $placeholder;
  }
  
  
}
