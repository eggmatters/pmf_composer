<?php
namespace core;
/**
 * Description of ModelBase
 *
 * 3670bf7ffa177ce82e3677a214861dac585ae7e3
 * @author meggers
 */

use core\connectors\Connector;

abstract class ModelBase {

  protected static abstract function getConnectorConfiguration();

  public function __construct(array $modelAttributes = null) {
    $this->setAttributes($modelAttributes);
  }

  public function setAttributes(array $modelAttributes = null) {
    if (empty($modelAttributes)) {
      return;
    }
    foreach ($modelAttributes as $name => $value) {
      if (class_exists($name)) {
        $classReflector = new \ReflectionClass($name);
        $classInstance  = $classReflector->newInstance($value);
        $this->{$this->resolveName($name)} = $classInstance;
      }
      if (is_array($value))  {
        $this->setCollection($value);
      } else if (is_object($value)) {
        $this->$name = $this->setObject($name, $value);
      } else {
        $this->$name = $value;
      }
    }
  }
  
  public static function getAll($eager = false) {
    $results = self::getModelConnector()->getAll($eager);
    return self::setCollection($results);
  }
  
  public static function get($id, $eager = false, $column = null) {
    $results = self::getModelConnector()->get($id, $eager, $column);
    return self::setObject($results);
  }
  
  public static function getByParent(\core\ControllerBase  $foreignController, $eager = false) {
    $results = self::getModelConnector()->getByParent($foreignController, $eager);
    return self::setCollection($results);
  }
  
  public static function getByJoin(\core\ControllerBase $foreignController, $eager = false) {
    $results = self::getModelConnector()->getByJoin($foreignController, $eager);
    return self::setCollection($results);
  }
  /**
   * 
   * @return core\connectors\DBConnector|core\connectors\APIConnector
   */
  public static function getConnector() {
    return self::getModelConnector();
  }
  
  public static function setCollection($collection) {
    $modelsCollection = [];
    foreach ($collection as $index => $values) {
      if (is_numeric($index)) {
        $classReflector = new \ReflectionClass(get_called_class());
        $classInstance  = $classReflector->newInstance($values);
        $modelsCollection[] = $classInstance;
      } else {
        $modelsCollection[$index] = $values;
      }
    }
    return $modelsCollection;
  }
  
  /**
   * 
   * @return Connector
   */
  private static function getModelConnector() {
    $className = get_called_class();
    $modelClass = new \ReflectionClass(get_called_class());
    return Connector::instantiate($className::getConnectorConfiguration(), $modelClass);
  }
  
  private static function setObject($modelObject) {
    $classReflector = new \ReflectionClass(get_called_class());
    $classInstance = $classReflector->newInstance($modelObject);
    return $classInstance;
  }
  
  private function resolveName($name) {
    return class_exists($name) ?
      resolver\Inflector::namespaceToModelName($name) :
        $name;
  }
}
