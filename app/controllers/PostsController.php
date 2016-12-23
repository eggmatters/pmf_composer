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
    $postsData = $posts->getAll();
    echo "<pre>";
    print_r($postsData);
    echo "</pre>";
  }
  
  public function get($argument) {
    $this->renderDebug("GOT HERE IN Index WITH GET: $argument");
  }
  
  public function renderDebug($msg) {
    echo "<pre>$msg</pre>";
  }
}
