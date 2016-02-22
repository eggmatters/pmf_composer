<?php
/**
 * Description of ControllerBase
 *
 * @author meggers
 */
namespace core;

abstract class ControllerBase {
  protected $resources;
  
  public function __construct() {
    
  }
  
  public static function getPath($url = null ) {
    $requestUri = filter_input(INPUT_SERVER, 'REQUEST_URI');
    $requestParams = explode('/', $requestUri);
    return $requestParams;
  }

}