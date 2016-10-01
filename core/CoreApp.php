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
  public static function routeRequest() {
    $request = self::getRequest();
    $requestObject = new RequestObject();
    $resourcesIterator = new SimpleIterator($request->getResourceArray());
    while ($resourcesIterator->hasNext()) {
      $resourceValue = $resourcesIterator->current();
      $controllerSet = $requestObject->setControllerNamespace($resourceValue);
      $dirSet = $requestObject->isResourceDirectory($resourceValue);
      if ($controllerSet) {
        $resourcesArray = $resourcesIterator->truncateFromIndex($resourcesIterator->getIndex());
        $reflectionClass = new \ReflectionClass($requestObject->getControllerNamespace());
        $controllerClass = $reflectionClass->newInstance($requestObject, $resourcesArray);
        $controllerClass->init();
        return;
      }
      if ( !$dirSet) {
        self::issue(404);
        return;
      }
    }
  }
  
  /**
   * Helper method used by controllers. Inspects a value from the URL 
   * and returns its corresponding MVC role.
   * 
   * @global Request $httpRequest
   * @return Request 
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
  
  public static function getRequest() {
    global $httpRequest;
    return $httpRequest;
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

