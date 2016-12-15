<?php

namespace core;

/**
 * Description of RequestObject
 *
 * @author matthewe
 */

class RequestObject {

  private $modelNamespace;
  private $controllerNamespace;
  private $tableName;
  private $viewPath;
  private $requestArguments;
  private $app;
  
  public function __construct(
    $modelNamespace        = ""
    , $controllerNamespace = ""
    , $tableName           = ""
    , $viewPath            = ""
    , $requestArguments    = []) {
    $this->app = dirname(__DIR__) . '/app';
    $this->modelNamespace      = empty($modelNamespace) ? 'app\\models' : $modelNamespace;
    $this->controllerNamespace = empty($controllerNamespace) ? 'app\\controllers' : $controllerNamespace;
    $this->viewPath            = empty($viewPath) ? $this->app . "/views" : $viewPath;
    $this->tableName           = $tableName;
    $this->requestArguments    = $requestArguments;
  }
  
  public function getModelNamespace() {
    return $this->modelNamespace;
  }
  
  public function getControllerNamespace() {
    return $this->controllerNamespace;
  }
  
  public function getViewPath() {
    return $this->viewPath;
  }
  
  public function getRequestArguments() {
    return $this->requestArguments;
  }
  /**
   * Sets the model namespace from a resource array entry
   * @param type $resourceValue
   * @param \core\RequestObject $requestObject
   * @return boolean|\core\RequestObject
   */
  public function setModelNamespace($resourceValue) {
    if ($resourceValue == "index.php") {
      $this->modelNamespace = $this->modelNamespace . '\\IndexModel';
      return true;
    }
    $modelName = Inflector::camelize(Inflector::singularize($resourceValue)) . "Model";
    $modelNamespace = $this->modelNamespace . '\\' . $modelName;
    if (class_exists($modelNamespace)) {
      $this->modelNamespace = $modelNamespace;
      return true;
    }
    return false;
  }

  /**
   * Sets the controller namespace from a resource array
   * @param string $resourceValue
   * @param \core\RequestObject $requestObject
   * @return boolean|\core\RequestObject
   */
  public function setControllerNamespace($resourceValue) {
    if ($resourceValue == "index.php") {
      $this->controllerNamespace = $this->controllerNamespace . '\\IndexController';
      return true;
    }
    $controllerName = Inflector::camelize($resourceValue) . "Controller";
    $controllerNamespace = '\\' . $this->controllerNamespace . '\\' . $controllerName;
    if (class_exists($controllerNamespace)) {
      
      $this->controllerNamespace = $controllerNamespace;
      return true;
    }
    return false;
  }
  
  public function setRequestArgument($requestArgument) {
    $this->requestArguments[] = $requestArgument;
  }
  
  public function isResourceDirectory($resource) {
    $pathBase = dirname(__DIR__);
    $namespacePath = preg_replace("/\\\/", "/", $this->controllerNamespace) . '/' . $resource;
    if (is_dir($pathBase . '/' . $namespacePath)) {
      $this->controllerNamespace .= '\\' . $resource;
      $this->modelNamespace .= '\\' . $resource;
      return true;
    }
    return false;
    
  }
}
