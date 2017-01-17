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
    /* @var $resolvedControllerArg resolver\ControllerArgs */
    $resolvedControllerArg = $resolver->resolveController($controllerArgs);
    if (is_null($resolvedControllerArg)) {
      self::issue("404");
      return;
    }
    ControllerBase::invokeMethod($request, $resolvedControllerArg);
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

}

