<?php

namespace configurations;

/**
 * Use this trait as a template to provide connection parameters to the database.
 * @author matthewe
 */
trait schemaConnectorTrait {
  protected static function getConnectorConfiguration() {
    return array(
      'Connector' => \core\connectors\DBConnector::class,
      'ConnectorConfig' => array(
        'host'   => 'localhost',
        'dbName' => 'pmf_db',
        'user'   => 'pmf_user',
        'pass'   => 'pmf_pass'
      ),
      'ConnectorCache' => \utilities\cache\DBCache::class,
      'Normalizer' => \utilities\normalizers\DBNormalizer::class
    );
  }
}
