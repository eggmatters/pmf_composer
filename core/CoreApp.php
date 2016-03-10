<?php
/**
 * 
 *
 * @author meggers
 */
namespace core;

class CoreApp {
  
  /**
   * Entry point for the application. Sets up request object
   * and instantiates a controller from the URL
   * 
   * @param string $requestUri
   */
  public static function routeRequest($requestUri = null) {
    $request = new Request($requestUri);
    $controllerClass = self::getControllerClassPath('Index');
    if (!empty($request->getResourceArray())) {
      $controllerClass = self::getControllerClassPath($request->getResourceArray()[0]);
    }
    if (class_exists($controllerClass)) {
      $reflectionClass = new \ReflectionClass($controllerClass);
      $controller  = $reflectionClass->newInstance($request);
      $controller->init();
    } else {
      self::issue("404");
    }
  }
  
  /**
   * Helper method used by controllers. Inspects a value from the URL 
   * and returns its corresponding MVC role.
   * 
   * @param string $resourceValue - a path in the URL
   * @return string
   */
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
  
  /**
   * Returns a fully qualified classpath to a defined model from 
   * a url path.
   * 
   * @param string $resourceValue
   * @return string
   */
  public static function getModelClassPath($resourceValue) {
    return "app\\models\\" . Inflector::camelize($resourceValue) . "Model";
  }
   /**
   * Returns a fully qualified classpath to a defined controller from 
   * a url path.
   * 
   * @param string $resourceValue
   * @return string
   */
  public static function getControllerClassPath($resourceValue) {
    if ($resourceValue == "index.php") {
      return "app\\controllers\\IndexController";
    }
    return "app\\controllers\\" . Inflector::camelize($resourceValue) . "Controller"; 
  }
  /**
   * responsible for issuing error pages (404, 500 etc.)
   * Will attempt to load corresponding template in application 
   * Set corresponding headers.
   * @param type $httpCode
   */
  public static function issue($httpCode) {
    die("Issue $httpCode here");
  }
}

