<?php

namespace core\resolver;

use core\CoreApp;
use core\Request;
use core\SimpleIterator;

/**
 * Utility class to resolve conventions for url paths, model & controller declarations,
 * table & api uri transformations etc.
 *
 * @author matthewe
 */
class Resolver {
  
  const NAMESPACE_SEPERATOR = "\\";
  
  private $appPath;
  
  private $namespaceBase;
  
  private $controllerIterator;
  
  private $modelIterator;
  
  private $controllerNamespaces;
  
  private $modelNamespaces;
  
  private $namespaceDirectories;

  
  public function __construct($appPath = null) {
    $this->appPath = (is_null($appPath)) ? CoreApp::rootDir() . DIRECTORY_SEPARATOR . "app"
      : CoreApp::rootDir() . DIRECTORY_SEPARATOR . $appPath;
    $this->namespaceBase = (is_null($appPath)) ? "app" : "\\" . $appPath;
    $this->controllerIterator = $this->getIterator($this->appPath . "/controllers");
    $this->modelIterator = $this->getIterator($this->appPath . "/models"); 
    $this->namespaceDirectories = [];
    $this->collectData(); 
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
   * 
   * @param Request $request
   * @return type
   */
  public function resolveRequest(Request $request) {
    $resourcesIterator = new SimpleIterator($request->getResourceArray());
    $controllerArgs = $this->parseResourceArray($resourcesIterator);
    return (count($controllerArgs) == 0) 
      ? array(self::resolveIndex($this->namespaceBase))
        : $controllerArgs;
  }
  
  public function resolveController(Request $request, array $controllerArgs) {
    /* @var $calledController ControllerArgs */
    $calledController = array_pop($controllerArgs);
    foreach($controllerArgs as $controllerArg) {
      $calledController->setArgument($controllerArg);
    }
    $method = $this->resolveControllerMethod($request, $calledController);
    if (is_null($method)) {
      return null;
    }
    $calledController->setMethod($method);
    return $calledController; 
  }
  
  public function resolveControllerMethod(Request $request, ControllerArgs $controllerArg) {
    $requestMethod      = $request->getHttpMethod();
    $methodPrefix       = $this->setMethodPrefix($requestMethod, $controllerArg);
    $methodCandidate    = array_reduce($controllerArg->getArguments(), function($currentArgument) 
      use ($controllerArg, $methodPrefix) {
        $method = $methodPrefix . Inflector::camelize($currentArgument);
        return ($controllerArg->isMethod($method)) ? $method : null;
      }, "");
    return (empty($methodCandidate)) ? $controllerArg->getMethodBySignature() : $methodCandidate;
  }
  
  public function collectData() {
    //check for caching here:
    $this->setControllerNamespaces();
    $this->setModelNamespaces();
  }
  
  public function setControllerNamespaces() {
    $this->controllerNamespaces = $this->setNamespaceArray($this->controllerIterator);
  }
  
  public function getControllerNamespaces() {
    return $this->controllerNamespaces;
  }
  
  public function setModelNamespaces() {
    $this->modelNamespaces = $this->setNamespaceArray($this->modelIterator);
  }
  
  public function getModelNamespaces() {
    return $this->modelNamespaces;
  }
  
  public static function resolveNamespaceFromResource($resourceValue, $type) {
    return Inflector::camelize($resourceValue) . $type;
  }
  
  public static function resolveMethodFromResource($resourceValue, $httpMethod) {
    return strtolower($httpMethod) . Inflector::camelize($resourceValue);
  }
  
  public static function resolveIndex($namespaceBase = "\\app") {
    return new ControllerArgs($namespaceBase . "\\controllers\IndexController");
  }
  
  private function getIterator($path) {
    return new \RecursiveIteratorIterator(
      new \RecursiveDirectoryIterator($path), \RecursiveIteratorIterator::SELF_FIRST);
  }
  
  private function setNamespaceArray(\RecursiveIteratorIterator $iterator) {
    $namespaceArray = [];
    foreach ($iterator as $appFile) {
      $filename = $appFile->getFilename();
      if($appFile->isFile()) {
        $namespace = Inflector::pathToNamespace(substr
            ($appFile->getPath(), strlen(CoreApp::rootDir()) + 1 )
          ) . "\\" . substr($filename, 0, strpos($filename, ".php") );
        if (\class_exists($namespace)) {
          $namespaceArray[] = $namespace;
        }
      }
      if($appFile->isDir() && strpos($filename, '.') === false) {
        $this->namespaceDirectories[] = $filename;
      }
    }
    return $namespaceArray;
  }
  
  private function parseResourceArray(SimpleIterator $resourcesIterator, ControllerArgs $controllerArgs = null, &$returnArray = []) {
    $namespaceBase = $this->namespaceBase . "\\controllers";
    $currentNamespace = $namespaceBase;
    while ($resourcesIterator->hasNext()) {
      $currentResource      = $resourcesIterator->current();
      $controllerBase       = self::resolveNamespaceFromResource($currentResource, "Controller");
      $controllerNamespace  = $currentNamespace . "\\" . $controllerBase;
      if (in_array($controllerNamespace, $this->controllerNamespaces)) {
        $controllerArgs = new ControllerArgs($controllerNamespace);
        $returnArray[]  = $controllerArgs;
        $resourcesIterator->next();
        $this->parseResourceArray($resourcesIterator, $controllerArgs, $returnArray);
      }
      else if (in_array($currentResource, $this->namespaceDirectories)) {
        $currentNamespace .= "\\"  . $currentResource;
      }
      else if (!is_null($controllerArgs)) {
        $controllerArgs->setArgument($currentResource);
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
  
  private function setMethodPrefix($httpMethod, ControllerArgs $controllerArg) {
    $arguments = (count($controllerArg->getArguments()) > 0) ? true : false;
    $new = array_search("new", $controllerArg->getArguments());
    $edit = array_search("edit", $controllerArg->getArguments());
    
    if ($httpMethod == "GET" && ($new !== false)) {
      return "new";
    }
    if ($httpMethod == "GET" && ($edit !== false)) {
      return "edit";
    }
    if ($httpMethod == "GET" && $arguments) {
      return "get";
    }
    if ($httpMethod == "GET" && $arguments === false) {
      return "index";
    }
    if ($httpMethod == "PUT" || $httpMethod == "PATCH") {
      return "update";
    }
    if ($httpMethod == "POST") {
      return "create";
    }
    if ($httpMethod == "DELETE") {
      return "delete";
    }
  }
}