<?php
namespace utilities\cache;
/**
 * ControllerCache - This class iterates through the file system, generating
 * all of the namespaces for existing controllers
 *
 * @author matthewe
 */
class ControllerCache extends CacheBase {
  
  const CONTROLLER_NAMESPACES = 'controller_namespaces';
  const NAMESPACE_DIRECTORIES = 'namespace_directories';

  private $controllerIterator;
  private $controllerNamespaces;

  public function __construct($appPath = null) {
    parent::__construct($appPath);
    $this->controllerIterator = $this->getIterator($this->appPath . "/controllers");
  }

  public function setControllerNamespaces() {
    $this->controllerNamespaces = $this->getCachedArray(self::CONTROLLER_NAMESPACES);
    if (is_null($this->controllerNamespaces)) {
      $this->controllerNamespaces = $this->setNamespaceArray($this->controllerIterator);
      $this->setCachedArray($this->controllerNamespaces, self::CONTROLLER_NAMESPACES);
    } 
  }
  
  public function getControllerNamespaces() {
    if (!isset($this->controllerNamespaces)) {
      $this->setControllerNamespaces();
    }
    return $this->controllerNamespaces;
  }

}
