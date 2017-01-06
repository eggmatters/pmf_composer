<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\controllers;
use core\ControllerBase;

/**
 * Description of UsersController
 *
 * @author meggers
 */
class UsersController extends ControllerBase {
  public function get(int $id) {
    $this->renderDebug("<pre>".print_r(\app\models\UserModel::get($id),1)."</pre>");
  }
  
  public function index() {
    $this->renderDebug("GOT HERE IN USers WITH INDEX");
  }
  
  public function renderDebug($msg) {
    echo "<pre>$msg</pre>";
  }
}
