<?php

spl_autoload_register(function($class) {
  $classpath = __DIR__ . "/" . str_replace('\\', '/', $class) . ".php";
  if (is_file($classpath)) {
   require_once $classpath;
   return true;
  } 
  return false;
});