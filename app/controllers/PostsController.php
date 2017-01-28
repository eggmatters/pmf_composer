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
    echo "<pre>";
    print_r($postsData);
    echo "</pre>";
  }
  
  public function indexAll(string $eager) {
    $postsData = PostModel::getAll(true);
    echo "<pre>";
    print_r($postsData);
    echo "</pre>";
  }


  public function get(int $id) {
    $postsData = PostModel::get($id, true);
    echo "<pre>";
    print_r($postsData);
    echo "</pre>";
  }
  
  public function getUserPosts(UsersController $user) {
    $userPosts = PostModel::getByParent($user, 1);
    echo "<pre>";
    print_r($userPosts);
    echo "</pre>";
  }
  public function getPostTags(TagsController $tags) {
    $tagPosts = PostModel::getByJoin($tags, 1);
    echo "<pre>";
    print_r($tagPosts);
    echo "</pre>";
  }
  
  public function getPostTags2(TagsController $tags, string $debug) {
    /* @var $connector \core\connectors\DBConnector */
    $connector = PostModel::getConnector();
    $constraints = new connectors\Constraints();
    $tagsValue = $tags->getControllerArgs()->getArguments()[0]->value;
    $sql = $connector->getQueryBase()
      ->Select($this->getModelNamespace(), $tags->getModelNamespace())
      ->LeftJoin('posts_tags', $this->getModelNamespace(), "posts_id" ,"id")
      ->LeftJoin($tags->getModelNamespace(), 'posts_tags',"id","tags_id")
      ->Where(
        $constraints->term('tags.id', '=', $tagsValue)
        );
    $connector->executeQuery($sql);
    $postTags = PostModel::setCollection($connector->normalizeResultsCollection($connector->getResultsSet(), $sql));
    echo "<pre>";
    print_r($postTags);
    echo "</pre>";
  }
}
