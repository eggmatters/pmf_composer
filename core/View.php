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
      $layoutFile = self::viewDir() 
        . DIRECTORY_SEPARATOR . "layouts"
        . DIRECTORY_SEPARATOR . $viewData['layout'] 
        . DIRECTORY_SEPARATOR .  "layout.php";
      $layoutData = isset($viewData['layoutData']) ? $viewData['layoutData'] : null;
      $view = self::layout($layoutFile, $layoutData, $view);
    }
    Response::renderHTML($view);
    
  }
  
  public static function  layout($layoutFile, $layoutData, $content) {
    ob_start();
    require_once($layoutFile);
    return ob_get_clean();
  }
  
  public static function partial($viewPath, $viewData) {
    
  }
}