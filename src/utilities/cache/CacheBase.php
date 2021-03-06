<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace utilities\cache;

use core\CoreApp;
use core\resolver\Inflector;
/**
 * Description of CacheBase
 *
 * @author matthewe
 */
abstract class CacheBase implements ICache {
  
  const NAMESPACE_SEPERATOR = "\\";
  
  protected $appPath;
  
  protected $namespaceBase;
  
  protected $namespaceDirectories;
  
  public function __construct($appPath = null) {
    $this->appPath = (is_null($appPath)) ? CoreApp::rootDir() . DIRECTORY_SEPARATOR . "app"
      : CoreApp::rootDir() . DIRECTORY_SEPARATOR . $appPath;
    $this->namespaceBase = (is_null($appPath)) ? "app" : "\\" . $appPath;
    $this->namespaceDirectories = [];
  }
  
  public function getAppPath() {
    return $this->appPath;
  }
  
  public function getNamespaceBase() {
    return $this->namespaceBase;
  }
  
  public function getNamespaceDirectories() {
    return $this->namespaceDirectories;
  }
  
  protected function getIterator($path) {
    return new \RecursiveIteratorIterator(
      new \RecursiveDirectoryIterator($path), \RecursiveIteratorIterator::SELF_FIRST);
  }
  
  protected function setNamespaceArray(\RecursiveIteratorIterator $iterator) {
    $namespaceArray = [];
    foreach ($iterator as $appFile) {
      $filename = $appFile->getFilename();
      if($appFile->isFile()) {
        $namespace = Inflector::pathToNamespace(substr
            ($appFile->getPath(), strlen(CoreApp::rootDir()) + 1 )
          ) . "\\" . substr($filename, 0, strpos($filename, ".php") );
        if (\class_exists($namespace)) {
          $namespaceArray[] = $namespace;
        }
      }
      if($appFile->isDir() && strpos($filename, '.') === false) {
        $this->namespaceDirectories[] = $filename;
      }
    }
    return $namespaceArray;
  }
  
  public static function isCacheEnabled() {
    return false;
  }
  public static function enableCache() {
    return false;
  }
  public function setCachedObject($object, $key) {
    
  }
  public function setCachedArray($array, $key) {
    
  }
  public function setCachedValue($value, $key) {
    
  }
  public function getCachedObject($key) {
    return false;
  }
  public function getCachedArray($key) {
    return null;
  }
  public function getCachedValue($key) {
    return null;
  }
}
