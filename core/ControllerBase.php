<?php
/**
 * Description of ControllerBase
 *
 * @author meggers
 */
namespace core;

abstract class ControllerBase {
  /**
   * determinants provided from URI
   * @var array $resources
   */
  protected $resources;
  /**
   * Instantiate Request object
   * @var Request $request
   */
  protected $request;
  
  protected $associatedModels;
  
  protected $controllerName;
  
  public function __construct(Request $request, $resources = null) {
    $this->request = $request;
    if (is_null($resources)) {
      $this->resources = $request->getResourceArray();
    } else {
      $this->resources = $resources;
    }
    $reflectionClass = new \ReflectionClass($this);
    $this->controllerName = $reflectionClass->getName();
  }
  
  protected function init() {
    $resourcesIterator = new SimpleIterator($this->resources);
    while ($resourcesIterator->hasNext()) {
      $resourceValue = $resourcesIterator->next();
      $resourceType = $this->getResourceType($resourceValue);
      switch ($resourceType) {
        case "controller":
          $this->loadController($resourceValue, $resourcesIterator->truncateFromIndex($resourcesIterator->getIndex()));
          return;
        case "int":
          
        
      }
    }
  }
  
  protected function get($id) {
    
  }
  
  protected function getAll() {
    
  }
  
  protected function getModelClass() {
    $reflectionClass = new \ReflectionClass($this);
    $className = $reflectionClass->getName();
    $classBase = str_replace($className, 'Controller', '');
    $testModel = "app//models//" . Inflector::singularize($classBase) . "Model";
    if (class_exists($testModel)) {
      return $testModel;
    }
    return null;
  }
  
  private function getResourceType($resourceValue) {
    if (class_exists(self::getControllerClassPath($resourceValue))) {
      return "controller";
    }   
    if (class_exists(self::getModelClassPath($resourceValue))) {
      return "model";
    }   
    if (is_numeric($resourceValue)) {
      return "int";
    }   
    return "string";
  }
  
  private function getModelClassPath($resourceValue) {
    return "app\\models\\" . Inflector::camelize($resourceValue);
  }
  
  private function getControllerClassPath($resourceValue) {
    return "app\\controllers\\" . Inflector::camelize($resourceValue); 
  }
  
  private function loadController($resourceValue, $resourceStack) {
    $controllerName = $this->getControllerClassPath($resourceValue);
    if ($controllerName == $this->controllerName) {
      return;
    }
    $reflectionClass = new \ReflectionClass($controllerName);
    $controllerInstance = $reflectionClass->newInstance($this->request, $resourceStack);
    $controllerInstance->init();
  }

}