<?php

namespace core;

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
abstract class Connector {
  
  protected $host;
  protected $dbName;
  protected $user;
  protected $port;
  protected $pass;
  protected $conntype;
  /**
   *
   * @var Request 
   */
  protected $request;
  protected $modelInstance;
  
  const DBCONN=1;
  const APICONN=2;
  
  abstract public function getAll();
  
  abstract public function get($id = null);
  
  abstract public function create($params);
  
  abstract public function update($params);
  
  abstract public function delete($params = null);

  public function __construct($modelConnector, Request $request = null, $modelInstance = null) {
    if ($modelConnector['ConnectorType'] == self::DBCONN) {
      $this->setDb($modelConnector['Connector']);
      $this->conntype = self::DBCONN;
    }
    if ($modelConnector['ConnectorType'] == self::APICONN) {
      $this->setAPI($modelConnector['Connector']);
      $this->conntype = self::APICONN;
    }
    $this->request = $request;
    $this->modelInstance = $modelInstance;
  }
  
  /**
   * This method sets and returns an array of values to be used by the ORM.
   * It will filter API requests and DB requests, setting dependencies to be 
   * handled by the Connectors for each.
   * 
   * @param array $resourceArray
   * 
   */
  public function parseResources($resourceArray) {
    $rv = [];
    $resourcesIterator = new SimpleIterator($resourceArray);
    $current = $resourcesIterator->current();
    while ($next = $resourcesIterator->next()) {    
      $currentType = CoreApp::getResourceType($current);
      $nextType = CoreApp::getResourceType($next);
      $currentValid = $this->validateResource($current);
      if ($currentType == 'controller' && $currentValid) {
        $rv['joins'][] = $current;
      }
      if ($nextType == 'int' && $currentValid) {
        $rv['constraints'][] = (object) array("resource" => $current, "value" => $next);
      }
      $current = $next;
    }
    return $rv;
  }
  
  protected function setDb($connector) {
    $reflectionClass = new \ReflectionClass($this);
    $className = $reflectionClass->getName();
    foreach($connector as $property => $value) {
      if (property_exists($className, $property)) {
        $this->$property = $value;
      }
    }
  }
  
  protected function setAPI($connector) {
  
  }
  
  protected function getModelInstanceName() {
    $modelReflector = new \ReflectionClass($this->modelInstance);
    $modelName  = $modelReflector->getName();
    return preg_replace("/.\w.*\\\([A-Za-z].*)/", "$1", $modelName);
  }
  
  private function validateResource($resource) {
    global $modelConnections;
    $resourceModel = Inflector::camelize(Inflector::singularize($resource)) . "Model";
    if ($resourceModel == $this->getModelInstanceName()) {
      return false;
    }
    if (!isset($modelConnections[$resourceModel])) {
      return false;
    }
    if ($modelConnections[$resourceModel]['ConnectorType'] != $this->conntype) {
      return false;
    }
    return true;
  }
}