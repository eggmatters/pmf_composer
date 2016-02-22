<?php
/**
 * 
 *
 * @author meggers
 */
namespace core;

class PMFApp {
  public function __construct() {
    
  }
  
  public static function toCamelCase($str) {
    $str = preg_replace('/^[_\-]/', "", $str);
    $str[0] = strtoupper($str[0]);
    $func = create_function('$c', 'return strtoupper($c[1]);');
    return preg_replace_callback('/[_\-]([a-z])/', $func, $str);
  }
  
  public static function getType($obj) {
    
  }
}
