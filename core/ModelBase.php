<?php
namespace core;
/**
 * Description of ModelBase
 *
 * 3670bf7ffa177ce82e3677a214861dac585ae7e3
 * @author meggers
 */
require_once CoreApp::rootDir() . '/configurations/ModelMapper.php';

use core\connectors\Connector;

abstract class ModelBase {
  /**
   *
   * @var Connector; 
   */
  
  protected static abstract function getConnectorConfiguration();

  public function __construct(array $modelAttributes = null) {
    $this->setAttributes($modelAttributes);
  }

  public function setAttributes(array $modelAttributes = null) {
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
  
  public static function getAll() {
    $results = self::getModelConnector()->getAll();
    return self::setCollection($results);
  }
  
  public static function get($id) {
    $results = self::getModelConnector()->get($id);
    return self::setObject($results);
  }
  /**
   * 
   * @return core\connectors\DBConnector|core\connectors\APIConnector
   */
  public static function getConnector() {
    return self::getModelConnector();
  }
  /**
   * 
   * @return Connector
   */
  private static function getModelConnector() {
    $className = get_called_class();
    $modelClass = new \ReflectionClass($className);
    return Connector::instantiate($className::getConnectorConfiguration(), $modelClass);
  }
  
  private static function setObject($modelObject) {
    $className = get_called_class();
    $classReflector = new \ReflectionClass($className);
    $classInstance = $classReflector->newInstance($modelObject);
    return $classInstance;
  }
  
  public static function setCollection(array $collection) {
    $modelsCollection = [];
    foreach($collection as $entity) {
      $rf = new \ReflectionClass(get_called_class());
      $modelInstance = $rf->newInstance($entity);
      $modelsCollection[] = $modelInstance;
    }
    return $modelsCollection;
  }
}
