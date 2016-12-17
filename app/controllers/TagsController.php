<?php

namespace app\controllers;
use core\ControllerBase;
/**
 * Description of TagsController
 *
 * @author meggers
 */
class TagsController extends ControllerBase {
  //put your code here
  public function get() {
    echo "<pre>";
    print_r($this->getParams($this));
    echo "</pre>";
  } 
}
