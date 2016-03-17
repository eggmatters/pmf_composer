<?php
namespace core;
/**
 * Description of ModelBase
 *
 * @author meggers
 */
abstract class ModelBase {
  
  private $request;
  private $modelAttributes;
  private $connector;
  
  public function __construct(Request $request, $modelAttributes = null) {
    $this->request = $request;
    $this->setAttributes($modelAttributes);
  }
  
  public function get($id) {
    //establish connector
    //perform fetch
    //call instance.
  }
  
  public function getAll() {
    //establish connector
    //perform fetch
    //call instance.
  }
  
  public function update($id, $params) {
    
  }
  
  public function create($params) {
    
  }
  
  public function setAttributes($modelAttributes = null) {
    if (empty($this->modelAttributes)) {
      return;
    }
    if (is_null($modelAttributes)) {
      $modelAttributes = $this->modelAttributes;
    }
    foreach ($modelAttributes as $name => $value) {
      if (is_array($value)) {
        $this->$name = $this->setArray($value);
      } else if (is_object($value)) {
        $this->$name = $this->setObject($name, $value);
      } else {
        $this->$name = $value;
      }
    }
  }
  
  private function setArray($array) {
    $returnArray = [];
    foreach ($array as $index => $arrayObject) {
      if (is_array($arrayObject)) {
        $returnArray[$index] = $this->setArray($arrayObject);
      } elseif (is_object($arrayObject) && is_string($index)) {
        $returnArray[$index] = $this->setObject($index, $arrayObject);
      } 
      else {
        $returnArray[$index] = $arrayObject;
      }
    }
    return $returnArray;
  }
  
  private function setObject($name, \stdClass $modelObject) {
    $className = 'app\\models\\' . Inflector::camelize($name) . "Model";
    if (\class_exists($className)) {
      $classReflector = new \ReflectionClass($className);
      $classInstance = $classReflector->newInstance($modelObject);
      return $classInstance;
    }
    return $modelObject;
  }
}
