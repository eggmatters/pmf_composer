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
  
  public function __construct(Request $request) {
    $this->request = $request;
    $this->resources = $request->getResourceArray();
  }
  
  protected function init() {
     
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

}