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
  
  public function getRoutingSignatures() {
    $this->routingSignatures = $this->getCachedArray(self::ROUTING_SIGNATURES);
    if (empty($this->routingSignatures)) {
      $this->routingSignatures = [];
      $this->buildRoutingSignatures();
    }
    return $this->routingSignatures;
  }
  
  public function buildRoutingSignatures() {
    $this->routingSignatures = [];
    foreach($this->controllerNamespaces as $controllerNamespace) {
      $rf = new \core\PMFReflectionClass($controllerNamespace);
      $reflectionMethods = $rf->getControllerMethods();
      foreach ($reflectionMethods as $reflectionMethod) {
        $key = $this->setSignature($reflectionMethod);
        if (!empty($key)) {
          $this->routingSignatures[$controllerNamespace][$key] = $reflectionMethod->name;
        }
      }
    }
  }
  
  private function setSignature(\ReflectionMethod $reflectionMethod) {
    $arguments = $reflectionMethod->getParameters();
    $methodCandidate = $this->methodSignatureComponent($reflectionMethod->name);
    if (empty($methodCandidate)) { return null; }
    $signature =  array_reduce($arguments, function($carry, $item) {
      return $carry . $item->getType();
    },$methodCandidate);
    return $signature;
  }
  
  private function methodSignatureComponent($methodName) {
    $candidates = [];
    preg_match("/^(get|update|index|edit|new|create|delete)/i", $methodName, $candidates);
    $methodCandidate = isset($candidates[1]) ?
      ( $candidates[1] == 'index' ||
        $candidates[1] == 'new' ||
        $candidates[1] == 'edit') ? 
      "get" : $candidates[1]
      : "";
    return $methodCandidate;
  }
}
