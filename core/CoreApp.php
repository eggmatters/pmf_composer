<?php
/**
 * 
 *
 * @author meggers
 */
namespace core;

class CoreApp {
  public function __construct() {
    
  }
  
  public static function routeRequest($requestUri = null) {
    $requestPath = ControllerBase::getPath($requestUri);
    
    return $requestPath;
  }
  
  public static function toCamelCase($str) {
    $str = preg_replace('/^[_\-]/', "", $str);
    $str[0] = strtoupper($str[0]);
    $func = create_function('$c', 'return strtoupper($c[1]);');
    return preg_replace_callback('/[_\-]([a-z])/', $func, $str);
  }
  
  private static function getControllerInstance($requestPath) {
    if (!is_array($requestPath)) {
      throw new Exception("Invalid Request Path");
    }
    $controllerInstance = null;
    if (count($requestPath) == 0) {
      $controllerInstance = ControllerBase::setInstance('Index');
    }
    
  }
}
