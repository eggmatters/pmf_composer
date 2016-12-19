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
  
  private $fileArray;
  
  private $controllerNamespaceArray;
  
  private $modelNamespaceArray;
  
  public function __construct(array $resourceArray, $appPath = null) {
    $this->appPath = (is_null($appPath)) ? \core\CoreApp::rootDir() . DIRECTORY_SEPARATOR . "app"
      : \core\CoreApp::rootDir() . DIRECTORY_SEPARATOR . $appPath;
    $this->resourceArray = $resourceArray;
    $this->fileArray = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($this->appPath), RecursiveIteratorIterator::SELF_FIRST);
  }
  
}