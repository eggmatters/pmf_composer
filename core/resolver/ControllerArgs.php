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
  
  private $routingCache;
  
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
      $this->routingCache = new \utilities\cache\RoutingCache();
  }
  
  public function getNamespace() {
    return $this->namespace;
  }
  
  /**
   * 
   * @return \ReflectionClass 
   */
  public function getReflectionClass() {
    return $this->reflectionClass;
  }
  /**
   * 
   * @param string $argument
   * @param int $position
   */
  public function setArgument($argument, $position = null) {
    $type = $this->getType($argument);
    $value = $argument;
    if ($type == 'core\resolver\ControllerArgs') {
      $type = $argument->namespace;
      /* @var $argument ControllerArgs */
      $argument->setMethod($argument->getMethodBySignature());
      $value = \core\ControllerBase::invokeInstance(\core\CoreApp::getRequest(), $argument);
    }
    if ($type == 'array') {
      $value = explode($this->reflectionClass->getConstant('ARRAY_DELIMITER'), $argument);
    }
    $this->arguments[] = (object) array(
      'value'    => $value,
      'type'     => $type,
      'position' => $position
    );
    usort($this->arguments, function($a, $b) {
      return ($a->position < $b->position) ? -1 : 1;
    });
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
  
  /**
   * 
   * @return \ReflectionMethod $method
   */
  public function getMethod() {
    return $this->method;
  }
  
  public function getArguments() {
    return $this->arguments;
  }
  
  public function getArgumentsValues() {
    return array_map(function($current) {return $current->value; }, $this->arguments);
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
    $signature  = $this->buildSignature(CoreApp::getRequest()->getHttpMethod());
    $routes = $this->routingCache->getRoutingSignatures();
    return isset($routes[$this->namespace][$signature]) ?
      $routes[$this->namespace][$signature] : null;
  }
  
  private function buildSignature($httpMethod) {
    return array_reduce($this->arguments, function($carry, $item) {
      return $carry . $item->type;
    }, strtolower($httpMethod));
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
  
  private function getType($value) {
    $type = gettype($value);
    if ($type == "object") {
      return get_class($value);
    } 
    if ($type == "string" && is_numeric($value)) {
      return (ctype_digit(preg_replace('/-/','',$value))) ?
        "int" : "float";
    }
    if ($type == "string" 
        && ($delimiter = $this->reflectionClass->getConstant("ARRAY_DELIMITER"))
          && strpos($value, $delimiter) !== false) {
      return "array";
    }
    return $type;
  }
}