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
  private $className;
  private $tableName;
  private $identifier;
  private $method;
  
  public function __construct(
    $namespacePath  = ""
    , $filesystemPath = ""
    , $className      = ""
    , $tableName      = "" 
    , $identifier     = NULL
    , $method         = NULL) {
    $this->namespacePath  = $namespacePath;
    $this->filesystemPath = $filesystemPath;
    $this->className      = $className;
    $this->tableName      = $tableName;
    $this->identifier     = $identifier;
    $this->method         = $method;
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
    
    
  }
  
  private static function setRequestObjects(SimpleIterator &$resource, RequestObject &$requestObject, $requestObjectsArray) {
    if (!$resource->hasNext()) {
      return $requestObjectsArray;
    }
    if (is_dir($requestObject->filesystemPath . $resource ) ||
        class_exists($this->namespacePath . "\"" . $resource )) {
      $requestObject->addFilesystemPathFromResource($resource);
      $requestObject->addNamespacePathFromResource($resource);
      self::setRequestObjects($resource->next(), $requestObject, $requestObjectsArray);
    }
  }
  
  private function addFilesystemPathFromResource($resource) {
    $this->filesystemPath .= "/" . $resource;
  }
  
  private function addNamespacePathFromResource($resource) {
    $this->namespacePath .= "\"" . $resource;
  }
  
  
}
