<?php

namespace app\controllers;
use core\ControllerBase;
use app\models\TagModel;
/**
 * Description of TagsController
 *
 * @author meggers
 */
class TagsController extends ControllerBase {
  
  public function index() {
    $tagsData = TagModel::getAll();
    echo "<pre>";
    print_r($tagsData);
    echo "</pre>";
  }
  
  public function get(int $id) {
    $tagsData = TagModel::get($id);
    echo "<pre>";
    print_r($tagsData);
    echo "</pre>";
  }
}
