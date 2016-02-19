<?php

spl_autoload_register(function($class) {
  $classpath = str_replace('\\', '/', $class);
  echo $classpath;
  require_once $classpath . ".php";
});