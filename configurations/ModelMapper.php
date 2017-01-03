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
  'ConnectorType' => \core\Connector::DBCONN,
  'Connector' => $devDbConn
);

$apiConnector = array(
  'ConnectorType' => \core\Connector::APICONN,
  'Connector' => $devAPIConn
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