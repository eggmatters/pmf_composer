<?php

spl_autoload_register(function($class) {
  $classpath = str_replace('\\', '/', $class);
  require_once $classpath . ".php";
});