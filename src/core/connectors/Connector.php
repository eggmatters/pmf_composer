<?php

namespace core\connectors;

use utilities\cache\ICache;
use utilities\normalizers\INormalizer;
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
   * @var \ReflectionClass  
   */
  protected $modelClass;
  
  /**
   *
   * @var \utilities\cache\ICache|null $connectorCache
   */
  protected $connectorCache;
  
  /**
   *
   * @var \utilities\normalizers\INormalizer|null $normalizer
   */
  protected $normalizer;
  
  abstract public function getAll();
  
  abstract public function get($id = null);

  abstract public function create($params);
  
  abstract public function update($id, $params);
  
  abstract public function delete($params = null);

  public function __construct(
    \ReflectionClass $modelClass = null
    , ICache $connectorCache = null
    , INormalizer $normalizer = null )
  {
    $this->modelClass = $modelClass;
    $this->connectorCache = $connectorCache;
    $this->normalizer = $normalizer;
  }
  
  public function setModelClass(\ReflectionClass $modelClass) {
    $this->modelClass = $modelClass;
  }
  
  public static function instantiate(array $connectorConfiguration, \ReflectionClass $modelClass = null) {
    $thisReflectionClass = new \ReflectionClass($connectorConfiguration['Connector']);
    $cacheReflector = new \ReflectionClass($connectorConfiguration['ConnectorCache']);
    $normalizerReflector = new \ReflectionClass($connectorConfiguration['Normalizer']);
    $connectorCache = $cacheReflector->newInstance();
    $normalizer = $normalizerReflector->newInstance();
    $connector = $thisReflectionClass->newInstance($modelClass, $connectorCache, $normalizer);
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