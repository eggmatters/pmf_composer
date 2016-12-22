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
  public function getControllerParameters($method) {
    $rfm = new \ReflectionMethod($this->namespace, $method);
    return $rfm->getParameters();
  }
  
  public function isControllerParameter($method, $param) {
    $params = $this->getParameters($method);
    return (empty(array_filter($params, function ($k, $v) use ($param) {
      return ($k == $param);
    }, ARRAY_FILTER_USE_BOTH)));
  }
}
/**
 * Interactive mode enabled

php > class S {
php { public function foo($a,$b,$c) {}
php { }
php > $s = new S();
php > $rf = new ReflectionClass('S');
php > $m = $rf->getMethods();
php > print_r($m);
Array
(
    [0] => ReflectionMethod Object
        (
            [name] => foo
            [class] => S
        )

)
php > $rfm = new ReflectionMethod('S','foo');
php > $params = $rfm->getParameters();
php > print_r($params);
Array
(
    [0] => ReflectionParameter Object
        (
            [name] => a
        )

    [1] => ReflectionParameter Object
        (
            [name] => b
        )

    [2] => ReflectionParameter Object
        (
            [name] => c
        )

)
php > foreach($params as $p) {
php { echo $p->getName() . "\n" . $p->getType() . "\n" . $p->getPosition(); . "\n"; }
PHP Parse error:  syntax error, unexpected '.' in php shell code on line 2
php > foreach($params as $p) {
php { echo $p->getName() . "\n" . $p->getType() . "\n" . $p->getPosition() . "\n"; }
a

0
b

1
c

2
php > 

 */