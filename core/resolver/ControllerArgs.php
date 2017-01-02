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
   * @param int $position
   */
  public function setArgument($argument, $position) {
    $type = CoreApp::getType($argument);
    $this->arguments[] = (object) array(
      'value'    => $argument,
      'type'     => $type,
      'position' => $position
    );
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
      if ($this->matchMethod($currentMethod, $httpMethod)) {
        return $currentMethod;
      }
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
    $methodPrefix = $this->getMethodPrefix($httpMethod);
    if (strpos($method->getName(), $methodPrefix) === false) {
      return false;
    }
    $methodParams = $method->getParameters();
    if (count($methodParams) != count($this->arguments)) {
      return false;
    }
    return $this->matchParams($methodParams);
  }
  
  private function matchParams($params) {
    $argSort = function($a, $b) {
      $aPos = (method_exists($a, 'getPosition')) ?
        $a->getPosition() : $a->position;
      $bPos = (method_exists($b, 'getPosition')) ?
        $b->getPosition() : $b->position;
      if ($aPos == $bPos) {
        return 0;
      }
      return ($aPos < $bPos) ? -1 : 1;
    };
    usort($this->arguments, $argSort);
    usort($params, $argSort);
    for($i = 0; $i < count($this->arguments); $i++) {
      $currentArgument = $this->arguments[$i];
      $currentParam = $params[$i];
      $paramType = $currentParam->getType();
      if (is_null($paramType) && (
        $currentArgument->type != ("string") &&
        $currentArgument->type != ("integer") 
        ) ) {
          return false;
      }
    }
    return true;
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