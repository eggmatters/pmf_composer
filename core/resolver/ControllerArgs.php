<?php

namespace core\resolver;

use core\CoreApp;
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
      $this->classMethods = [];
      $this->setClassMethods($namespace);
  }
  
  public function getNamespace() {
    return $this->namespace;
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
      return ($method->class == $namespace);
    });
    return $this->classMethods;
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
    $httpMethod = CoreApp::getRequest()->getHttpMethod();
    $methodsIterator = new \core\SimpleIterator($this->getClassMethods());
    $currentMethod = $methodsIterator->current();
    while ($methodsIterator->hasNext()) {
      $currentMethod = $methodsIterator->next();
      
      
    }
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
  
  private function matchMethod(\ReflectionMethod $method, $httpMethod) {
    $rArray = [];
    $methodPrefix = $this->getMethodPrefix($httpMethod);
    if (strpos($method->getName(), $methodPrefix) === false) {
      return null;
    }
    $argumentsIterator = new \core\SimpleIterator($this->arguments);
    if ($argumentsIterator->size() != count($this->arguments)) {
      return null;
    }
    $currentArgument = $argumentsIterator->current();
    
    return $rArray;
  }
  
  private function matchParams($arguments, $params) {
    
    /* while ($paramsIterator->hasNext()) {
      /* @var $currentParam \ReflectionParameter 
      if (is_a($argument, 'core\\resolver\\ControllerArgs')
          &&
          $currentParam->getClass() == $argument->namespace) {
        return (object) array('reflectionParam' => $currentParam, 'value' => $argument );
      }
      if (is_a($argument, 'core\\resolver\\ControllerArgs')
          &&
          $currentParam->getClass() != $argument->namespace) {
        return null;
      }
       
    } * */
  }
  
  private function getMethodPrefix($httpMethod) {
    $arguments = (count($this->getArguments()) > 0) ? true : false;
    $new = array_search("new", $this->getArguments());
    $edit = array_search("edit", $this->getArguments());
    
    if ($httpMethod == "GET" && ($new !== false)) {
      return "new";
    }
    if ($httpMethod == "GET" && ($edit !== false)) {
      return "edit";
    }
    if ($httpMethod == "GET" && $arguments) {
      return "get";
    }
    if ($httpMethod == "GET" && $arguments === false) {
      return "index";
    }
    if ($httpMethod == "PUT" || $httpMethod == "PATCH") {
      return "update";
    }
    if ($httpMethod == "POST") {
      return "create";
    }
    if ($httpMethod == "DELETE") {
      return "delete";
    }
  }
}