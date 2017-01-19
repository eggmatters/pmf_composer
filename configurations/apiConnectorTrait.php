<?php

namespace configurations;

/**
 *
 * @author matthewe
 */
trait apiConnector {
  protected static function getConnectorConfiguration() {
    return array(
      'Connector' => \core\connectors\APIConnector::class,
      'ConnectorConfig' => array()
    );
  }
}
