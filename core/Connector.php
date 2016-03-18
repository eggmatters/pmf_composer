<?php

namespace core;

/**
 * This class will instigate and abstract the process of fetching persisted data.
 * What we wish to accomplish is to obtain formatted data from *any* connection.
 * 
 * Our connections, at this point will be relegated to databases and RESTful API's. 
 * Each model will, by convention, have one connection type associated with it. 
 * The connections themselves will be descendants of one of two abstract classes: PDOConn, or APIConn. 
 * Each will provide abstract methods as well as defined methods and properties for returning
 * formatted data. Formatted data will be defined by abstract methods shared by both classes
 * along the nature of toJSON, toXML, etc. 
 * 
 * Derived connection classes will follow the same naming convention of MVC. 
 *
 * @author meggers
 */
abstract class Connector {
  //put your code here
  protected $host;
  protected $dbName;
  protected $user;
  protected $port;
  protected $pass;
  protected $conntype;
  
  const DBCONN=1;
  const APICONN=2;
  
  abstract public function conn();
  
  abstract public function getAll($resource);
  
  abstract public function get($resource);
  
  abstract public function create($resource, $params);
  
  abstract public function update($resource, $params);
  
  abstract public function delete($resource, $params = null);

  public function __construct($modelConnector) {
    if ($modelConnector['ConnectorType'] == self::DBCONN) {
      $this->setDb($modelConnector['Connector']);
      $this->conntype = self::DBCONN;
    }
    if ($modelConnector['ConnectorType'] == self::APICONN) {
      $this->setAPI($modelConnector['Connector']);
      $this->conntype = self::APICONN;
    }
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
  
  protected function setAPI($modelConnection) {
    
  }
}
