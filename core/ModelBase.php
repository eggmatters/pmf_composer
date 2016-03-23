<?php
namespace core;
/**
 * Description of ModelBase
 *
 * @author meggers
 */
abstract class ModelBase {
  
  public function __construct($modelAttributes = null) {
    $this->setAttributes($modelAttributes);
  }
  
  public static function get($id = null, $resources = null) {
    if (is_null($resources)) {
      $resources = CoreApp::getRequest()->getResources();
    }
    $modelClass = self::setModelInstance($resources);
    $connector = self::setConnector($modelClass);
    if (is_null($connector)) { 
      return $connector;
    }
    $rs = $connector->get($id);
    if ($rs) {
      $modelClass->setAttributes($rs[0]);
    }
    return $modelClass;
  }
  
  public static function getAll($resources = null) {
    if (is_null($resources)) {
      $resources = CoreApp::getRequest()->getResources();
    }
    $modelClass = self::setModelInstance();
    $connector = self::setConnector($modelClass);
    if (is_null($connector)) { 
      return $connector;
    }
    $models = [];
    $rs = $connector->getAll();
    if (is_array($rs)) {
      foreach ($rs as $row) {
        $rowModel = self::setModelInstance();
        $rowModel->setAttributes($row);
        $models[] = $rowModel;
      }
    }
    return $models;
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
  
  private static function setModelInstance() {
    $calledClass = get_called_class();
    $modelReflector = new \ReflectionClass($calledClass);
    return $modelReflector->newInstance();
  }
  
  private static function setConnector() {
    global $modelConnections;
    $connector = null;
    $modelClass = get_called_class(); 
    $className = preg_replace("/.\w.*\\\([A-Za-z].*)/", "$1", $modelClass);
    switch ($modelConnections[$className]['ConnectorType']) {
      case Connector::DBCONN:
        $connector = new DBConnector($modelConnections[$className], $modelClass);
        break;
      case Connector::APICONN:
        $connector = new APIConnector($modelConnections[$className], $modelClass);
        break;
    }
    return $connector;
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
