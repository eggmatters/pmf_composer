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
    
    /**
     * Resolver will determine list of ControllerArgs to call, determining 
     * final method from url:
     * 
     * /users/1:
     * Users->getUsers(1)
     * 
     * /users/1/posts
     * Posts->getUserPosts(ControllerArgs $users)
     * 
     * /users/1/tags/smelly/funny/posts
     * Posts->getUserPostsByTag(ControllerArgs $users, ControllerArg $tags)
     * 
     * $users = (
     *   "UsersController" ; 
     *   "UsersModel", 
     *   view_file,
     *   $args => 1 )
     * 
     * $tags  = ( . . .
     *   $args => "smelly","funny"
     * 
     * The ControllerArgs is merely a meta-data container containing resolvable
     * information about the request. 
     */
    
    
    
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

