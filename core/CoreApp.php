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
  public static function routeRequest(Request $request) {
    $resolver = new resolver\Resolver();
    $controllerArgs = $resolver->resolveRequest($request);
    $calledController = $resolver->resolveController($request, $controllerArgs);
    echo "<pre>";
    print_r($calledController);
    echo "</pre>";    
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
  
  public static function rootDir() {
    return dirname(__DIR__);
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
  
  function microtime_float() {
    list($usec, $sec) = explode(" ", microtime());
    return ((float) $usec + (float) $sec);
  }

}

