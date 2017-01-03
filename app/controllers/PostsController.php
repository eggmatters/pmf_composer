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
  const ARRAY_DELIMITER = ":";
  public function index() {
    $posts = new \app\models\PostModel();
    $postsData = $posts->getAll();
    echo "<pre>";
    print_r($postsData);
    echo "</pre>";
  }
  
  public function get(string $argument) {
    $this->renderDebug("GOT HERE IN Index WITH GET: $argument");
  }
  
  public function renderDebug($msg) {
    echo "<pre>$msg</pre>";
  }
  
  public function getUserPostsTest(UsersController $user
    , int $position
    , string $tag
    , string $flap
    , float $float
    , array $array) {
    echo "<h1>POSTS</h2>";
    echo "<pre>";
    print_r($user);
    echo "\n";
    echo($position);
    echo "\n";
    echo($tag);
    echo "\n";
    echo($flap);
    echo "\n";
    echo($float);
    echo "\n";
    print_r(join(",", $array));
    echo "</pre>";
  }
}
