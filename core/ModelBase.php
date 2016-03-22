<?php
namespace core;
/**
 * Description of ModelBase
 *
 * @author meggers
 */
abstract class ModelBase {
  
  /**
   *
   * @var Request 
   */
  protected $request;
  /**
   *
   * @var Connector 
   */
  protected $connector;
  
  public function __construct(Request $request, $modelAttributes = null) {
    $this->request = $request;
    $this->setAttributes($modelAttributes);
    $this->setConnector();
  }
  
  public function get($id = null) {
    $rs = $this->connector->get($id);
    if ($rs) {
      $this->setAttributes($rs[0]);
    }
  }
  
  public function getAll() {
  }
  
  public function update($id, $params) {
    
  }
  
  public function create($params) {
    
  }
  
  public function setAttributes($modelAttributes = null) {
    if (empty($modelAttributes)) {
      return;
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
  
  private function setConnector() {
    global $modelConnections;
    $modelReflector = new \ReflectionClass($this);
    $this->connector = null;
    $className = preg_replace("/.\w.*\\\([A-Za-z].*)/", "$1", $modelReflector->getName());
    switch ($modelConnections[$className]['ConnectorType']) {
      case Connector::DBCONN:
        $this->connector = new DBConnector($modelConnections[$className], $this->request, $this);
        break;
      case Connector::APICONN:
        $this->connector = new APIConnector($modelConnections[$className], $this);
        break;
    }
  }
}
