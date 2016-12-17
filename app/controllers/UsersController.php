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
  public function get() {
    echo "<pre>";
    print_r($this->getParams($this));
    echo "</pre>";
  }
}
