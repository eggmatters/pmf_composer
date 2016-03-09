<?php
/**
 * 
 *
 * @author meggers
 */
namespace core;

class CoreApp {
  
  public static function routeRequest($requestUri = null) {
    $request = new Request($requestUri);
    if (empty($request->getResourceArray())) {
      $controller = new \app\controllers\IndexController($request);
    } else {
      $controllerClass = self::getControllerClassPath($request->getResourceArray()[0]);
      $reflectionClass = new \ReflectionClass($controllerClass);
      $controller      = $reflectionClass->newInstance($request);
    }   
    if (!isset($controller)) {
      return;
    }   
    $controller->init();
    
  }
  
  public static function issue($httpCode) {
    die("Issue $httpCode here");
  }
}

