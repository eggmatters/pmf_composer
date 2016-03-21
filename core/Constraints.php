<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace core;

/**
 * Description of Constraints
 *
 * @author meggers
 */
class Constraints {
  private $constraints;
  
  public function __construct() {
    $this->constraints = "";
  }
  
  public function term($lhs, $operator, $rhs) {
    $this->constraints .= $this->setTerm($lhs, $operator, $rhs);
    return $this;
  }
  
  public function andTerm($lhs = "", $operator = "", $rhs = "") {
    $this->constraints .= "AND " . $this->setTerm($lhs, $operator, $rhs);
    return $this;
  }
  
  public function orTerm($lhs = "", $operator = "", $rhs = "") {
    $this->constraints .= "OR "  . $this->setTerm($lhs, $operator, $rhs);
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
  
  private function setTerm($lhs = "", $operator = "", $rhs = "") {
    return trim("$lhs $operator $rhs") . " ";
  }
  
  
}
