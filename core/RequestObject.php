<?php

namespace core;

/**
 * Description of RequestObject
 *
 * @author matthewe
 */

class RequestObject {
  const CONTROLLER = 1;
  const MODEL = 2;
  const VIEW = 3;
  
  private $parent;
  private $modelNamespace;
  private $controllerNamespace;
  private $modelClassPath;
  private $controllerClassPath;
  private $tableName;
  private $viewPath;
  private $uri;
  private $app;
  
  public function __construct(
    \core\RequestObject $parent = null
    , $modelNamespace           = ""
    , $controllerNamespace = ""
    , $modelClassPath      = ""
    , $controllerClassPath = ""
    , $tableName           = ""
    , $viewPath            = ""
    , $uri                 = "") {
    $this->app = dirname(__DIR__) . '/app';
    $this->parent = $parent;
    $this->modelNamespace      = empty($modelNamespace) ? 'app\\models' : $modelNamespace;
    $this->controllerNamespace = empty($controllerNamespace) ? 'app\\controllers' : $controllerNamespace;
    $this->modelClassPath      = empty($modelClassPath) ? $this->app . "/models" : $modelClassPath;
    $this->controllerClassPath = empty($controllerClassPath) ? $this->app . "/controllers" : $controllerClassPath;
    $this->viewPath            = emtpy($viewPath) ? $this->app . "/views" : $viewPath;
    $this->tableName           = $tableName;
    $this->uri                 = $uri;
  }
  
  public function __set($name, $value) {
    $this->$name = $value;
  }
  
  public function __get($name) {
    return $this->$name;
  }
  
  public static function setFromResources($resources) {
    $resourcesIterator = new SimpleIterator($resources);
    $requestObject = new RequestObject("app", dirname(__DIR__) . "/app");
    return self::setRequestObjects($resourcesIterator, $requestObject, []);
  }
  
  private static function setRequestObjects(SimpleIterator &$resource, RequestObject &$requestObject, $requestObjectsArray) {
    
    
  }
  
  private static function buildRequestPropsFromResource($resource) {
    
  }
}
