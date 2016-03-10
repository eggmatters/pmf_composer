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
      if (class_exists($controllerClass)) {
        $reflectionClass = new \ReflectionClass($controllerClass);
      } else {
        self::issue("404");
      }
      $controller      = $reflectionClass->newInstance($request);
    }   
    if (!isset($controller)) {
      return;
    }   
    $controller->init();
    
  }
  
  
  public static function getResourceType($resourceValue) {
    if (class_exists(self::getControllerClassPath($resourceValue))) {
      return "controller";
    }   
    if (class_exists(self::getModelClassPath($resourceValue))) {
      return "model";
    }   
    if (is_numeric($resourceValue)) {
      return "int";
    }   
    return "string";
  }
  
  public static function getModelClassPath($resourceValue) {
    return "app\\models\\" . Inflector::camelize($resourceValue);
  }
  
  public static function getControllerClassPath($resourceValue) {
    if ($resourceValue == "index.php") {
      return "app\\controllers\\IndexController";
    }
    return "app\\controllers\\" . Inflector::camelize($resourceValue); 
  }
  
  public static function issue($httpCode) {
    die("Issue $httpCode here");
  }
}

