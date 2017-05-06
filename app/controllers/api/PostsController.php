<?php

namespace app\controllers\api;
use core\ControllerBase;

/**
 * Description of subClass
 *
 * @author matthewe
 */
class PostsController extends ControllerBase {
  //put your code here
  public function get() {
    echo "<pre>";
    print_r($this->getParams($this));
    echo "</pre>";
  }
}
