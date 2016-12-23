<?php

namespace core\resolver;

/**
 * Description of ResourceType
 *
 * @author matthewe
 */
class ControllerArgs {

  /**
   *
   * @var string $namespace 
   */
  private $namespace;
  
  /**
   *
   * @var \ReflectionClass $reflectionClass
   */
  private $reflectionClass;
  
  /**
   *
   * @var array $arguments 
   */
  private $arguments;

  public function __construct($namespace) 
  {
      $this->namespace = $namespace;
      $this->reflectionClass = new \ReflectionClass($namespace);
      $this->arguments = [];
  }
  /**
   * 
   * @param string $argument
   */
  public function setArgument($argument) {
    $this->arguments[] = $argument;
  }
  /**
   * 
   * @param array $arguments
   */
  public function setArguments(array $arguments) {
    $this->arguments = $arguments;
  }
  /**
   * 
   * @return Array
   */
  public function getMethods() {
    return $this->reflectionClass->getMethods();
  }
  
  public function getArguments() {
    return $this->arguments;
  }
  /**
   * 
   * @param string $method
   * @return boolean
   */
  public function isMethod($method) {
    return $this->reflectionClass->hasMethod($method);
  }
  /**
   * 
   * @param string $method
   * @return array
   */
  public function getMethodParameters($method) {
    $rfm = new \ReflectionMethod($this->namespace, $method);
    return $rfm->getParameters();
  }

  public function getMethodBySignature() {
    $methods = $this->getMethods();
    foreach($methods as $method) {
      $this->matchMethodParametersWithArguments($method);
    }
  }
  
  public function matchMethodParametersWithArguments($method) {
    $parameters = $this->getMethodParameters($method);
    if (count($parameters != count($this->arguments))) {
      return 0;
    }
    $argumentTypes = array_map(function($argument) {
       if (gettype($argument) == "object") {
        return get_class($argument);
      } else {
        return gettype($argument);
      }
    }, $this->arguments);
    $matches = array_reduce($parameters, function($count, $param) use ($argumentTypes) {
      $type = $param->getType();
      if (in_array($param->getType(), $argumentTypes)) {
        return $count++;
      }
    }, 0);
  }
}