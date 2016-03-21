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
  protected $request;
  
  const DBCONN=1;
  const APICONN=2;
  
  abstract public function getAll();
  
  abstract public function get();
  
  abstract public function create($params);
  
  abstract public function update($params);
  
  abstract public function delete($params = null);

  public function __construct($modelConnector, Requet $request = null) {
    if ($modelConnector['ConnectorType'] == self::DBCONN) {
      $this->setDb($modelConnector['Connector']);
      $this->conntype = self::DBCONN;
    }
    if ($modelConnector['ConnectorType'] == self::APICONN) {
      $this->setAPI($modelConnector['Connector']);
      $this->conntype = self::APICONN;
    }
    $this->request = $request;
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
