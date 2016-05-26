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
  
  private $namespacePath;
  private $filesystemPath;
  private $controllerClass;
  private $modelClass;
  private $uri;
  
  public function __construct(
    $namespacePath     = ""
    , $filesystemPath  = ""
    , $controllerClass = ""
    , $modelClass      = ""
    , $tableName       = "" 
    , $uri             = "") {
    $this->namespacePath   = $namespacePath;
    $this->filesystemPath  = $filesystemPath;
    $this->controllerClass = $controllerClass;
    $this->modelClass      = $modelClass;
    $this->tableName       = $tableName;
    $this->uri             = $uri;
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
    $currentResource = $resource->current();
    $resource->next();
    $filesystemPath = $requestObject->filesystemPath . '/' . $currentResource;
    if (is_dir($filesystemPath)) {
      $requestObject->filesystemPath = $filesystemPath;
      $requestObject->namespacePath .= '\\' . $currentResource;
      $this->setRequestObjects($resource, $requestObject, $requestObjectsArray);
    } 
    $controllerName = Inflector::camelize($currentResource) . 'Controller';
    $modelName = Inflector::camelize(Inflector::singularize($currentResource)) . 'Model';
    $controllerClass = $requestObject->namespacePath .= '\\controllers\\' . $controllerName;
    $modelClass = $requestObject->namespacePath .= '\\models\\' . $modelName;
    if (class_exists($modelClass)) {
      $requestObject->modelClass = $modelClass;
    }
    if (class_exists($controllerClass)) {
      $requestObject->controllerClass = $controllerClass;
      $this->setRequestObjects($resource, $requestObject, $requestObjectsArray);
    }
    $requestObject->uri = (empty($requestObject->uri)) ? $currentResource : $requestObject->uri . "/" . $currentResource;
    $requestObjectsArray[] = $requestObject;
    $newRequestObject = new RequestObject("app", dirname(__DIR__) . "/app");
    if ($resource->hasNext()) {
      $this->setRequestObjects($resource, $newRequestObject, $requestObjectsArray);
    } else {
      return $requestObjectsArray;
    }
  }
}
