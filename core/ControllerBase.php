<?php
/**
 * Description of ControllerBase
 *
 * @author meggers
 */
namespace core;

abstract class ControllerBase {
  protected $resources;
  protected $loadLevel;
  
  public function __construct(array $resources = [], $loadLevel = false) {
    
  }

}