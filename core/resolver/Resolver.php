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
  
  private $controllerIterator;
  
  private $modelIterator;
  
  private $controllerNamespaces;
  
  private $modelNamespaces;
  
  private $namespaceDirectories;

  
  public function __construct($appPath = null) {
    $this->appPath = (is_null($appPath)) ? CoreApp::rootDir() . DIRECTORY_SEPARATOR . "app"
      : CoreApp::rootDir() . DIRECTORY_SEPARATOR . $appPath;
    $this->controllerIterator = $this->getIterator($this->appPath . "/controllers");
    $this->modelIterator = $this->getIterator($this->appPath . "/models"); 
    $this->namespaceDirectories = [];
    $this->collectData(); 
  }
  
  public function resolveRequest(Request $request) {
    $resourcesIterator = new SimpleIterator($request->getResourceArray());
    return $this->parseResourceArray($resourcesIterator);
    
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
            ($appFile->getPath(), strlen(CoreApp::rootDir() ) )
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
    $namespaceBase = "\\app\\controllers";
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
      else {
        return null;
      }
      $resourcesIterator->next();
    }
    return $returnArray;
  }
}