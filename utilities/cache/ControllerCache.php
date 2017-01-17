<?php
namespace utilities\cache;
/**
 * ControllerCache - This class iterates through the file system, generating
 * all of the namespaces for existing controllers
 *
 * @author matthewe
 */
class ControllerCache {
  
  private $controllerIterator;
  
  private $controllerNamespaces;
  
  private function __construct($appPath = null) {
    parent::__construct($appPath);
    $this->controllerIterator = $this->getIterator($this->appPath . "/controllers");
 }
 
 public function setControllerNamespaces() {
    $this->controllerNamespaces = $this->setNamespaceArray($this->controllerIterator);
  }
  
}
