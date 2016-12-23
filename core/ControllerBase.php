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
  
  
  public function __construct(Request $request, resolver\ControllerArgs $controllerArgs) {
    $this->request = $request;
    $this->controllerArgs = $controllerArgs;
  }

  public function init() {
  
  }
}