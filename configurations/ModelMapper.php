<?php
namespace configurations;

$devDbConn = array(
  'host'   => 'localhost',
  'dbName' => 'pmf_db',
  'user'   => 'pmf_user',
  'pass'   => 'pmf_pass'
);

$modelConnections = array(
   'IndexModel' => array(
     'ConnectorType' => 'none'
   ),
   'TestModel' => array(
     'ConnectorType' => \core\Connector::DBCONN,
     'Connector' => $devDbConn
   )
);