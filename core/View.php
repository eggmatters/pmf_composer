<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace core;

/**
 * Description of View
 *
 * @author matthewe
 */
class View {
  
  public static function cssBase() {
    return CoreApp::rootDir() . "/html/css";
  }
  
  public static function jsBase() {
    return CoreApp::rootDir() . "/html/js";
  }
  
  public static function viewDir() {
    return CoreApp::appDir() . "/views";
  }
  
  public static function render($viewPath, array $viewData) {
    ob_start();
    require_once($viewPath);
    $view = ob_get_clean();
    if (isset($viewData['layout'])) {
      $layoutFile = self::viewDir() . "/" . $viewData['layout'] . "/layout.php";
      $layoutData = isset($viewData['layoutData']) ? $viewData['layoutData'] : null;
      $view = $this->layout($layoutFile, $layoutData, $view);
    }
    Response::renderHTML($view);
    
  }
  
  public static function  layout($layoutFile, $layoutData, $content) {
    
  }
  
  public static function partial($viewPath, $viewData) {
    
  }
}