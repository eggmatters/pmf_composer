<?php
namespace configurations;

$devDbConn = array(
  'host'   => 'localhost',
  'dbName' => 'pmf_db',
  'user'   => 'pmf_user',
  'pass'   => 'pmf_pass'
);

$schemaConnector = array(
  'ConnectorType' => \core\Connector::DBCONN,
  'Connector' => $devDbConn
);

$modelConnections = array(
  'IndexModel' => array(
    'ConnectorType' => 'none'
  ),
  'PostModel' => array(
    'ConnectorType' => \core\Connector::DBCONN,
    'Connector' => $devDbConn
  ),
  'UserModel' => array(
    'ConnectorType' => \core\Connector::DBCONN,
    'Connector' => $devDbConn
  ),
  'TagModel' => array(
    'ConnectorType' => \core\Connector::DBCONN,
    'Connector' => $devDbConn
  )
);