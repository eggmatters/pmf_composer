<?php
/**
 * Description of IndexController
 *
 * @author meggers
 */
namespace app\controllers;
use core\ControllerBase;

class IndexController extends ControllerBase {

  public function get() {
    $this->renderDebug("GOT HERE IN Index WITH GET");
  }
  
  public function index() {
    $this->renderDebug("GOT HERE IN Index WITH INDEX");
  }
  
  private function renderDebug($msg) {
    echo "<pre>$msg</pre>";
  }
}
