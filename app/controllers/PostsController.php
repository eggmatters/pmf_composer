<?php

namespace app\controllers;
use core\ControllerBase;
/**
 * Description of TestController
 *
 * @author meggers
 */
class PostsController extends ControllerBase {
  //put your code here
  protected function index() {
    $posts = new \app\models\PostModel();
    
  }
}
