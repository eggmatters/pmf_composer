<?php

namespace app\controllers;
use core\ControllerBase;
use app\models\PostModel;
/**
 * Description of TestController
 *
 * @author meggers
 */
class PostsController extends ControllerBase {
  const ARRAY_DELIMITER = ":";
  
  public function index() {
    $postsData = PostModel::getAll();
    echo "<pre>got here";
    print_r($postsData);
    echo "</pre>";
  }
  
  public function get(int $id) {
    $posts = new \app\models\PostModel();
    $postsData = $posts->get($id);
    echo "<pre>";
    print_r($postsData);
    echo "</pre>";
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
