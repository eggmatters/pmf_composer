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
  
  private $resourceArray;
  
  private $controllerIterator;
  
  private $modelIterator;
  
  private $controllerNamespaceArray;
  
  private $modelNamespaceArray;
  
  private $benchmark;

  
  public function __construct($appPath = null) {
    $this->appPath = (is_null($appPath)) ? \core\CoreApp::rootDir() . DIRECTORY_SEPARATOR . "app"
      : \core\CoreApp::rootDir() . DIRECTORY_SEPARATOR . $appPath;
    $this->controllerIterator = $this->getIterator($this->appPath . "/controllers");
    $this->modelIterator = $this->getIterator($this->appPath . "/models"); 
  }
  
  private function getIterator($path) {
    return new \RecursiveIteratorIterator(
      new \RecursiveDirectoryIterator($path), \RecursiveIteratorIterator::SELF_FIRST);
  }
  
  public function setControllerNamespaces() {
    foreach ($this->controllerIterator as $controllerFile) {
      if($controllerFile->isFile()) {
        $filename = $controllerFile->getFilename();
        $namespace = Inflector::pathToNamespace(substr
            ($controllerFile->getPath(), strlen(\core\CoreApp::rootDir() ) )
          ) . "\\" . substr($filename, 0, strpos($filename, ".php") );
        echo $namespace . "\n";
        
      }
    }
  }
  
}