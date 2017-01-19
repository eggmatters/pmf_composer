<?php
namespace app\models;
use core\ModelBase;
/**
 * Description of PostModel
 *
 * @author matthewe
 */
class PostModel extends ModelBase {
  /**
   * schemaConnectorTrait is a defined trait which provides the 
   * required abstract function: protected static function getConnectorConfiguration()
   */
  use \configurations\schemaConnectorTrait;
}
