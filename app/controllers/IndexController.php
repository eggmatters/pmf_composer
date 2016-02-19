<?php
/**
 * Description of IndexController
 *
 * @author meggers
 */
namespace app\controllers;
use core\ControllerBase;

class IndexController extends ControllerBase {

  public function __construct(array $resources = [], $loadLevel = false) {
    $this->resources = $resources;
    $this->loadLevel = $loadLevel;
  }
}
