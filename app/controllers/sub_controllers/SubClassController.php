<?php

namespace app\controllers\sub_controllers;
use core\ControllerBase;

/**
 * Description of subClass
 *
 * @author matthewe
 */
class subClassController extends ControllerBase {
  //put your code here
  public function get() {
    echo "<pre>";
    print_r($this->getParams($this));
    echo "</pre>";
  }
}
