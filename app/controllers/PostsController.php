<?php

namespace app\controllers;
use core\ControllerBase;
use core\resolver\ControllerArgs;
/**
 * Description of TestController
 *
 * @author meggers
 */
class PostsController extends ControllerBase {
  //put your code here
//  public function index() {
//    $posts = new \app\models\PostModel();
//    $postsData = $posts->getAll();
//    echo "<pre>";
//    print_r($postsData);
//    echo "</pre>";
//  }
//  
//  public function get(string $argument) {
//    $this->renderDebug("GOT HERE IN Index WITH GET: $argument");
//  }
//  
//  public function renderDebug($msg) {
//    echo "<pre>$msg</pre>";
//  }
  
  public function getUserPosts(UsersController $user) {
    echo "<h1>POSTS</h2>";
    echo "<pre>";
    print_r($user);
    echo "</pre>";
  }
}
