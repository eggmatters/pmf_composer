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
   * @var string type 
   */
  private $namespace;

  public function __construct(
    $namespace) 
  {
      $this->namespace = $namespace;
  }
  
  public function getMethods() {
    $rf = new \ReflectionClass($this->namespace);
    return $rf->getMethods();
  }
  
  public function isMethod($method) {
    
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