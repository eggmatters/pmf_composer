<?php
/**
 * Controller base is the abstract base class from which application controllers
 * are derived. 
 *
 * @author meggers
 */
namespace core;

use resolver\Resolver;
use resolver\ControllerArgs;

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
  /**
   * 
   * @return Request
   */
  protected function getRequest() {
    return $this->request;
  }
  /**
   * 
   * @return core\resolver\ControllerArgs
   */
  protected function getControllerArgs() {
    return $this->controllerArgs;
  }
  
  public static function invokeInstance(Request $request, \core\resolver\ControllerArgs $controllerArgs) {
    return $controllerArgs->getReflectionClass()->newInstance($request, $controllerArgs);
  }
  
  public static function invokeMethod(Request $request, \core\resolver\ControllerArgs $controllerArgs) {
    $requestedController = self::invokeInstance($request, $controllerArgs);
    $requestedControllerMethod = $controllerArgs->getMethod();
    $requestedControllerMethod->invokeArgs($requestedController, $controllerArgs->getArgumentsValues());
    return $requestedController;
  }
}