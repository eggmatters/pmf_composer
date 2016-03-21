<?php
namespace core;
/**
 * Description of ModelBase
 *
 * @author meggers
 */
abstract class ModelBase {
  
  private $request;
  private $connector;
  private $modelAttributes;
  
  
  public function __construct(Request $request, $modelAttributes = null) {
    $this->request = $request;
    $this->setAttributes($modelAttributes);
    $this->connector = $this->setConnector();
  }
  
  public function get($id = null) {
    $this->connector->get($id);
  }
  
  public function getAll() {
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
  
  public function setConnector() {
    global $modelConnections;
    $this->connector = null;
    $className = preg_replace("/.\w.*\\\([A-Za-z].*)/", "$1", __CLASS__);
    switch ($modelConnections[$className]['ConnectorType']) {
      case Connector::DBCONN:
        $this->connector = new DBConnector($modelConnections[$className], $this);
        break;
      case Connector::APICONN:
        $this->connector = new APIConnector($modelConnections[$className], $this);
        break;
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
