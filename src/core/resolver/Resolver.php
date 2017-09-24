<?php

namespace core\resolver;

use core\CoreApp;
use core\Request;
use core\SimpleIterator;
use utilities\cache\ControllerCache;

/**
 * Utility class to resolve conventions for url paths, model & controller declarations,
 * table & api uri transformations etc.
 *
 * @author matthewe
 */
class Resolver {  
  /**
   *
   * @var utilities\cache\ControllerCache 
   */
  private $controllerCache;

  
  public function __construct($appPath = null) {
    $this->controllerCache = new ControllerCache($appPath);
    $this->controllerCache->setControllerNamespaces();
  }
  
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
   * /users/1/tags/smelly+funny/posts
   * Posts->getUserPostsByTag(ControllerArgs $users, ControllerArg $tags)
   * 
   * The ControllerArgs is merely a meta-data container containing resolvable
   * information about the request. 
   * 
   * @param Request $request
   * @return type
   */
  public function resolveRequest(Request $request) {
    $resourcesIterator = new SimpleIterator($request->getResourceArray());
    $controllerArgs = $this->parseResourceArray($resourcesIterator);
    return (count($controllerArgs) == 0) 
      ? array(self::resolveIndex($this->controllerCache->getNamespaceBase()))
        : $controllerArgs;
  }
  
  public function resolveController(array $controllerArgs) {
    /* @var $calledController ControllerArgs */
    $calledController = array_pop($controllerArgs);
    foreach($controllerArgs as $controllerArg) {
      $calledController->setArgument($controllerArg);
    }
    $method = $this->resolveControllerMethod($calledController);
    if (is_null($method)) {
      return null;
    }
    $calledController->setMethod($method);
    return $calledController; 
  }
  
  public function resolveControllerMethod(ControllerArgs $controllerArg) {
    return  $controllerArg->getMethodBySignature();
  }
  
  public static function resolveNamespaceFromResource($resourceValue, $type) {
    return Inflector::camelize($resourceValue) . $type;
  }
  
  public static function resolveMethodFromResource($resourceValue, $httpMethod) {
    return strtolower($httpMethod) . Inflector::camelize($resourceValue);
  }
  
  public static function resolveIndex($namespaceBase = "app") {
    return new ControllerArgs($namespaceBase . "\\controllers\IndexController");
  }
  
  private function parseResourceArray(SimpleIterator $resourcesIterator, ControllerArgs $controllerArgs = null, &$returnArray = []) {
    $namespaceBase = $this->controllerCache->getNamespaceBase() . "\\controllers";
    $currentNamespace = $namespaceBase;
    $argPosition = 0;
    while ($resourcesIterator->hasNext()) {
      $currentResource      = $resourcesIterator->current();
      $controllerBase       = self::resolveNamespaceFromResource($currentResource, "Controller");
      $controllerNamespace  = $currentNamespace . "\\" . $controllerBase;
      if (in_array($controllerNamespace, $this->controllerCache->getControllerNamespaces())) {
        $controllerArgs = new ControllerArgs($controllerNamespace);
        $returnArray[]  = $controllerArgs;
        $resourcesIterator->next();
        $this->parseResourceArray($resourcesIterator, $controllerArgs, $returnArray);
      }
      else if (in_array($currentResource, $this->controllerCache->getNamespaceDirectories())) {
        $currentNamespace .= "\\"  . $currentResource;
      }
      else if (!is_null($controllerArgs)) {
        $controllerArgs->setArgument($currentResource, $argPosition);
        $argPosition++;
      }
      else if ($currentResource == "index.php") {
        $returnArray[] = self::resolveIndex();
      }
      else {
        return null;
      }
      $resourcesIterator->next();
    }
    return $returnArray;
  }
}