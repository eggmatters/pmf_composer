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
    $this->connector = self::setConnector();
  }
  
  public static function get($id = null, $resources = null) {
    $modelClass = self::setInstanceFromResources($resources);
    $connector = self::setConnector();
    $rs = $connector->get($id);
    if ($rs) {
      $modelClass->setAttributes($rs[0]);
    }
    return $modelClass;
  }
  
  public static function getAll() {
  }
  
  public static function update($id, $params) {
    
  }
  
  public static function create($params) {
    
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
  
  private static function setInstanceFromResources($resources) {
    $request = new Request($resources);
    $calledClass = get_called_class();
    $modelReflector = new \ReflectionClass($calledClass);
    return $modelReflector->newInstance($request, $resources);
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
  
  private static function setConnector() {
    global $modelConnections;
    $connector = null;
    $className = preg_replace("/.\w.*\\\([A-Za-z].*)/", "$1", get_called_class());
    switch ($modelConnections[$className]['ConnectorType']) {
      case Connector::DBCONN:
        $connector = new DBConnector($modelConnections[$className], $this->request, $this);
        break;
      case Connector::APICONN:
        $connector = new APIConnector($modelConnections[$className], $this);
        break;
    }
    return $connector;
  }
}
