<?php

namespace core\connectors;

use utilities\cache\ICache;
/**
 * This class will instigate and abstract the process of fetching persisted data.
 * What we wish to accomplish is to obtain formatted data from *any* connection.
 * 
 * Our connections, at this point will be relegated to databases and RESTful API's. 
 * Each model will, by convention, have one connection type associated with it. 
 * The connections themselves will be descendants of one of two abstract classes: DBConnector, or APIConnector. 
 * Each will provide abstract methods as well as defined methods and properties for returning
 * formatted data. Formatted data will be defined by abstract methods shared by both classes
 * along the nature of toJSON, toXML, etc. 
 *
 * @author meggers
 */
abstract class Connector implements IConnector {
  
  /**
   *
   * @var int $conntype 
   */
  protected $conntype;
  /**
   *
   * @var \ReflectionClass  
   */
  protected $modelClass;
  
  /**
   *
   * @var \utilities\cache\ICache|null $connectorCache
   */
  protected $connectorCache;
  
  const DBCONN=1;
  const APICONN=2;
  
  abstract public function getAll();
  
  abstract public function get($id = null);

  abstract public function create($params);
  
  abstract public function update($id, $params);
  
  abstract public function delete($params = null);

  public function __construct(int $conntype, \ReflectionClass $modelClass = null, ICache $connectorCache = null) {
    $this->conntype = $conntype;
    $this->modelClass = $modelClass;
    $this->connectorCache = $connectorCache;
  }
  
  public function setModelClass(\ReflectionClass $modelClass) {
    $this->modelClass = $modelClass;
  }
  
  public static function instantiate(array $connectorConfiguration, \ReflectionClass $modelClass = null) {
    $thisReflectionClass = new \ReflectionClass($connectorConfiguration['Connector']);
    $conntype = null;
    switch ($thisReflectionClass->name) {
      case "core\connectors\DBConnector":
        $conntype = self::DBCONN;
        $connectorCache = new \utilities\cache\DBCache();
        break;
      case "core\connectors\APIConnector" :
        $conntype = self::APICONN;
         $connectorCache = null;
        break;
      default:
        throw new \Exception("Conntype for connector {$thisReflectionClass->name} not defined");
    }
    $connector = $thisReflectionClass->newInstance($conntype, $modelClass, $connectorCache);
    $connector->setProperties($connectorConfiguration, $thisReflectionClass->name);
    return $connector;
  }
  
  private function setProperties(array $modelConnector, string $className) {
    $connectorProps = $modelConnector['ConnectorConfig'];
    foreach($connectorProps as $property => $value) {
      if (property_exists($className, $property)) {
        $this->$property = $value;
      }
    }
  }
}