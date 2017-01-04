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
  'Connector' => \core\connectors\DBConnector::class,
  'ConnectorConfig' => $devDbConn
);

$apiConnector = array(
  'Connector' => \core\connectors\APIConnector::class,
  'ConnectorConfig' => $devAPIConn
);

trait schemaConnector {
  protected static function getConnectorConfiguration() {
    global $schemaConnector;
    return $schemaConnector;
  }
}

trait apiConnector {
  protected static function getConnectorConfiguration() {
    global $apiConnector;
    return $apiConnector;
  }
}