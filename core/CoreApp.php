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
  public static function routeRequest(Request $request, SimpleIterator $resourcesIterator, $requestObject = null) {
    $requestObject = is_null($requestObject) ? new RequestObject() : $requestObject;
    $resourceValue = $resourcesIterator->current();
    if ( $requestObject->setControllerNamespace($resourceValue) ) {
      $resourcesArray = $resourcesIterator->truncateFromIndex($resourcesIterator->getIndex());
      $reflectionClass = new \ReflectionClass($requestObject->getControllerNamespace());
      $controllerClass = $reflectionClass->newInstance($requestObject, $resourcesArray);
      $controllerClass->init();
      return;
    }
    if ( $requestObject->isResourceDirectory($resourceValue) ) {
      $resourcesIterator->next();
      self::routeRequest($request, $resourcesIterator, $requestObject);
    } else {
      //Determine whether or not to redirect here.
      self::issue(404);
      return;
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

