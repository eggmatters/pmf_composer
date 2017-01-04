<?php
namespace core;
/**
 * Description of ModelBase
 *
 * 3670bf7ffa177ce82e3677a214861dac585ae7e3
 * @author meggers
 */
require_once CoreApp::rootDir() . '/configurations/ModelMapper.php';

abstract class ModelBase {

  protected static abstract function getConnectorConfiguration();

  public function __construct($modelAttributes = null) {
    $this->setAttributes($modelAttributes);
    return $this;
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
  
  public function setConnector(Connector $connector) {
    $this->connector = $connector;
  }
  
  public function getConnector() {
    return $this->connector;
  }
  
  public static function getAll() {
    $results = self::getConfiguredConnector()->getAll();
    return self::setCollectionFromPDOArray($results);
  }
  
  private static function getConfiguredConnector() {
    $connector = isset($this->connector);
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
  
  private function setObject($name, \stdClass $modelObject) {
    $className = 'app\\models\\' . Inflector::camelize($name) . "Model";
    if (\class_exists($className)) {
      $classReflector = new \ReflectionClass($className);
      $classInstance = $classReflector->newInstance($modelObject);
      return $classInstance;
    }
    return $modelObject;
  }
  
  private static function setCollectionFromPDOArray($collection) {
    $modelsCollection = [];
    foreach($collection as $entity) {
      $rf = new \ReflectionClass(get_called_class());
      $modelInstance = $rf->newInstance($entity);
      $modelsCollection[] = $modelInstance;
    }
    return $modelsCollection;
  }
}
