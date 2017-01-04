<?php
namespace configurations;

$devDbConn = array(
  'host'   => 'localhost',
  'dbName' => 'pmf_db',
  'user'   => 'pmf_user',
  'pass'   => 'pmf_pass'
);

$devAPIConn = array(
  
);

$schemaConnector = array(
  'Connector' => \core\DBConnector::class,
  'ConnectorConfig' => $devDbConn
);

$apiConnector = array(
  'Connector' => \core\APIConnector::class,
  'ConnectorConfig' => $devAPIConn
);

trait schemaConnector {
  protected static function getModelConnector() {
    global $schemaConnector;
    return $schemaConnector;
  }
}

trait apiConnector {
  protected static function getModelConnector() {
    global $apiConnector;
    return $apiConnector;
  }
}