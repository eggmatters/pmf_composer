<?php
namespace core;
/**
 * Description of ModelBase
 *
 * @author meggers
 */
abstract class ModelBase {
  
  private $modelAttributes;
  
  public function __construct(\stdClass $modelAttributes = null) {
    $this->modelAttributes = $modelAttributes;
    $this->setAttributes();
  }
  
  public static function get($id) {
    //establish connector
    //perform fetch
    //call instance.
  }
  
  public static function getAll() {
    //establish connector
    //perform fetch
    //call instance.
  }
  
  public static function update($id, $params) {
    
  }
  
  public static function create($params) {
    
  }
  
  protected function setAttributes($modelAttributes = null) {
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
    $className = 'app\\models\\' . PMFApp::toCamelCase($name) . "Model";
    if (\class_exists($className)) {
      $classReflector = new \ReflectionClass($className);
      $classInstance = $classReflector->newInstance($modelObject);
      return $classInstance;
    }
    return $modelObject;
  }
}
