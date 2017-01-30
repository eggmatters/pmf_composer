<?php

namespace utilities\cache;

/**
 * Description of RoutingCache
 *
 * @author matthewe
 */
class RoutingCache extends CacheBase {
  const ROUTING_SIGNATURES = 'routing_signatures';
  
  private $controllerNamespaces;
  private $routingSignatures;
  
  public function __construct($appPath = null) {
    parent::__construct($appPath);
    $this->controllerNamespaces = (new ControllerCache($appPath))->getControllerNamespaces();
  }
  
  public function setRoutingSignatures() {
    $this->routingSignatures = $this->getCachedArray(self::ROUTING_SIGNATURES);
    if (empty($this->routingSignatures)) {
      $this->buildRoutingSignatures();
    }
  }
  
  public function buildRoutingSignatures() {
    $this->routingSignatures = [];
    foreach($this->controllerNamespaces as $controllerNamespace) {
      $rf = new \ReflectionClass($controllerNamespace);
      $reflectionMethods = $rf->getMethods();
    }
  }
}
