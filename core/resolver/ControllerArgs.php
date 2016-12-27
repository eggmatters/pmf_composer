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
   * @var array $classMethods
   */
  private $classMethods;
  
  /**
   *
   * @var \ReflectionMethod $reflectionMethod 
   */
  private $method;
  /**
   *
   * @var array $arguments 
   */
  private $arguments;
  
  /**
   *
   * @var \ReflectionClass $reflectionClass
   */
  private $reflectionClass;

  public function __construct($namespace) 
  {
      $this->namespace = $namespace;
      $this->reflectionClass = new \ReflectionClass($namespace);
      $this->method = null;
      $this->arguments = [];
      $this->setClassMethods($namespace);
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
  public function setClassMethods($namespace) {
    $methods =  $this->reflectionClass->getMethods();
    $this->classMethods = array_filter($methods, function($method) use ($namespace) {
      $c = $method->class;
      return ($method->class == $namespace);
    });
  }
  
  public function getClassMethods() {
    return $this->classMethods;
  }
  
  public function getArguments() {
    return $this->arguments;
  }
  
  public function setMethod($method) {
    if (is_string($method)) {
      $this->setMethodByString($method);
    } else if (is_a($method, \ReflectionMethod::class)) {
      $this->method = $method;
    }
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
    $methods = $this->getClassMethods();
    foreach($methods as $method) {
      $matches = $this->matchMethodParametersWithArguments($method);
      if ($matches = count($this->arguments)) {
        return $method;
      }
    }
    return null;
  }
  
  private function matchMethodParametersWithArguments(\ReflectionMethod $method) {
    $parameters = $this->getMethodParameters($method->getName());
    if (count($parameters) != count($this->arguments)) {
      return 0;
    }
    $argumentTypes = array_map(function($argument) {
       if (gettype($argument) == "object") {
        return get_class($argument);
      } else {
        return gettype($argument);
      }
    }, $this->arguments);
    $matches =  array_reduce($parameters, function($count, $param) use ($argumentTypes) {
      $type = $param->getType();
      if ( is_null($type) || in_array($type, $argumentTypes) ) {
        return $count++;
      }
    }, 0);
    return $matches;
  }
  
  private function setMethodByString($method) {
    foreach ($this->getClassMethods() as $classMethod) {
      /* @var $classMethod \ReflectionMethod */
      if ($classMethod->getName() == $method) {
        $this->method = $method;
        return;
      }
    } 
  }
}