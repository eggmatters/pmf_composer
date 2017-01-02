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
    /* @var $requestedController resolver\ControllerArgs */
    $requestedController = $resolver->resolveController($request, $controllerArgs);
    if (is_null($requestedController)) {
      self::issue("404");
      return;
    }
   
  }
  
  /**
   * 
   * @global Request $httpRequest
   * @return Request
   */
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
  
  public static function microtime_float() {
    list($usec, $sec) = explode(" ", microtime());
    return ((float) $usec + (float) $sec);
  }
  
  public static function getType($value) {
    $type = gettype($value);
    if ($type == "object") {
      $type = get_class($value);
    }
    return $type;
  }

}

