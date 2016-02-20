<?php
namespace core;
/**
 * Description of ModelBase
 *
 * @author meggers
 */
abstract class ModelBase {
  public function __construct(stdObject $modelAttributes = null) {
    ;
  }
  
  public function get() {
    echo "I am base";
  }
  
  
  
}
