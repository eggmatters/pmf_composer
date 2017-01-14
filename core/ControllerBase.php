<?php
/**
 * Controller base is the abstract base class from which application controllers
 * are derived. 
 *
 * @author meggers
 */
namespace core;

use core\resolver\Inflector;
use core\resolver\ControllerArgs;

abstract class ControllerBase {

  /**
   * @var Request $request
   */
  protected $request;
  
  /**
   *
   * @var core\resolver\ControllerArgs
   */
  protected $controllerArgs;
  
  
  public function __construct(Request $request, \core\resolver\ControllerArgs $controllerArgs) {
    $this->request = $request;
    $this->controllerArgs = $controllerArgs;
  }
  
  public function getModelNamespace() {
    return self::fetchModelNamespace($this->getNamespace());
  }
  
  /**
   * 
   * @return \core\resolver\ControllerArgs
   */
  public function getControllerArgs() {
    return $this->controllerArgs;
  }
  /**
   * 
   * @return Request
   */
  protected function getRequest() {
    return $this->request;
  }
  
  protected function getMethodArguments() {
    return array_map(function($cur) { return $cur->value; }, $this->controllerArgs->getArguments());
  }
  
  protected function getNamespace() {
    $rf = new \ReflectionClass($this);
    return $rf->getName();
  }
  
  /**
   * 
   * @param \core\Request $request
   * @param ControllerArgs $controllerArgs
   * @return ControllerBase
   */
  public static function invokeInstance(Request $request, ControllerArgs $controllerArgs) {
    return $controllerArgs->getReflectionClass()->newInstance($request, $controllerArgs);
  }
  /**
   * 
   * @param \core\Request $request
   * @param ControllerArgs $controllerArgs
   * @return ControllerBase
   */
  public static function invokeMethod(Request $request, ControllerArgs $controllerArgs) {
    $requestedController = self::invokeInstance($request, $controllerArgs);
    $requestedControllerMethod = $controllerArgs->getMethod();
    $requestedControllerMethod->invokeArgs($requestedController, $controllerArgs->getArgumentsValues());
    return $requestedController;
  }
  
  public static function fetchModelReflector(string $controllerNamespace) {
    $reflector = Inflector::swapControllerNamespaceToModel($controllerNamespace);
    if (class_exists($reflector)) {
      return new \ReflectionClass($reflector);
    }
    return null;
  }
  
  public static function fetchModelNamespace(string $controllerNamespace) {
    return Inflector::swapControllerNamespaceToModel($controllerNamespace);
  }
}