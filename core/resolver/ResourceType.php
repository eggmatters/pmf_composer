<?php

namespace core\resolver;

/**
 * Description of ResourceType
 *
 * @author matthewe
 */
class ResourceType {
  
  const CONTROLLER = 1;
  const MODEL      = 2;
  /**
   *
   * @var string type 
   */
  private $type;
  /**
   *
   * @var array 
   */
  private $controllerArgs;
  /**
   *
   * @var array 
   */
  private $domainArgs;
  /**
   *
   * @var string
   */
  private $resourceIndex;
  
  public function __construct(
    $type = null
    , $controllerArgs = null
    , $domainArgs = null
    , $resourceIndex = null) 
    {
      $this->type = $type;
      $this->controllerArgs = $controllerArgs;
      $this->domainArgs = $domainArgs;
      $this->resourceIndex = $resourceIndex;
  }
  
  public function getType() {
    return $this->type;
  }
  
  public function setType($type) {
    $this->type = $type;
    return $this;
  }
  
  public function getControllerArgs() {
    return $this->controllerArgs;
  }
  
  public function setControllerArgs($controllerArgs) {
    $this->controllerArgs = $controllerArgs;
    return $this;
  }
  
  public function getDomainArgs() {
    return $this->domainArgs;
  }
  
  public function setDomainArgs($domainArgs) {
    $this->domainArgs = $domainArgs;
    return $this;
  }
  
  public function getResourceIndex() {
    return $this->resourceIndex;
  }
  
  public function setResourceIndex($resourceIndex) {
    $this->resourceIndex = $resourceIndex;
    return $this;
  }
}
