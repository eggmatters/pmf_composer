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
  
  private $connector;
  
  public function __construct($modelAttributes = null) {
    $this->setAttributes($modelAttributes);
    $this->connector = $this->getConfiguredConnector();
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
  
  private function getConfiguredConnector() {
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
  
  public function getConnector() {
    return $this->connector;
  }
}
