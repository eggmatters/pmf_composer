<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace core;

/**
 * Description of ViewBase
 *
 * @author matthewe
 */
abstract class ViewBase {
  protected static abstract function render($viewPath, Response $response);
}
