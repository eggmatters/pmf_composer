<?php
namespace app\models;
use core\ModelBase;

/**
 * Description of UserModel
 *
 * @author matthewe
 */
class UserModel extends ModelBase {
  const allowedFields = ['id','user_name','user_role'];
  use \configurations\schemaConnectorTrait;
}
