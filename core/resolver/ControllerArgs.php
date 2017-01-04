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
    $httpMethod = CoreApp::getRequest()->getHttpMethod();
    $methodsIterator = new \core\SimpleIterator($this->getClassMethods());
    $currentMethod = $methodsIterator->current();
    while ($methodsIterator->hasNext()) {
      if ($this->matchMethod($currentMethod, $httpMethod)) {
        usort($this->arguments, function($a, $b) {
          return ($a->position < $b->position) ? -1 : 1;
        });
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
    $status = true;
    $aw = array_walk($this->arguments, function($argument, $index) use ($params, &$status) {
      $type = $argument->type;
      $matchingParams = array_filter($params, function($param) use ($type) {
        if (is_null($param->getType())) {
          $name = $param->getName();
          $message = "Controller: $type Parameter $name is not defined in signature." 
            . "\nPlease typehint parameters in controller methods";
          throw new \Exception($message );
        }
        $dbg = $param->getType()->__toString();
        return ($param->getType()->__toString() == $type);
      });
      if (count($matchingParams) > 1) {
        $param = \core\SimpleIterator::findBy($matchingParams, function($currentParam) use ($index, $type) {
          for($i = 0; $i < $index; $i++) {
            $currentArgument = $this->arguments[$i];
            if ($currentArgument->type == $type && $currentArgument->position == $currentParam->getPosition()) {
              return false;
            }
          }
          return true;
        });
        $argument->position = $param->getPosition();
      } else if (!empty($matchingParams)) {
        $param = $matchingParams[array_keys($matchingParams)[0]];
        $argument->position = $param->getPosition();
      } else {
        $status = false;
      }
    });
    return ($aw && $status);
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