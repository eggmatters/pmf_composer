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
  
  public function __construct($modelAttributes = null) {
    $this->setAttributes($modelAttributes);
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
}
