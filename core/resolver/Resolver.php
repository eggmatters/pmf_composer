<?php

namespace core\resolver;

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

  
  public function __construct($appPath = null) {
    $this->appPath = (is_null($appPath)) ? \core\CoreApp::rootDir() . DIRECTORY_SEPARATOR . "app"
      : \core\CoreApp::rootDir() . DIRECTORY_SEPARATOR . $appPath;
    $this->controllerIterator = $this->getIterator($this->appPath . "/controllers");
    $this->modelIterator = $this->getIterator($this->appPath . "/models"); 
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
      if($appFile->isFile()) {
        $filename = $appFile->getFilename();
        $namespace = Inflector::pathToNamespace(substr
            ($appFile->getPath(), strlen(\core\CoreApp::rootDir() ) )
          ) . "\\" . substr($filename, 0, strpos($filename, ".php") );
        if (class_exists($namespace)) {
          $namespaceArray[] = $namespace;
        }
        
      }
    }
    return $namespaceArray;
  }
  
}