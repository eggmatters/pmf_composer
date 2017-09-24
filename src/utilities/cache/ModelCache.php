<?php
namespace utilities\cache;

/**
 * Description of ModelCache
 *
 * @author meggers
 */
class ModelCache extends CacheBase {
  const MODEL_NAMESPACES = 'model_namespaces';
  
  private $modelIterator;
  private $modelNamespaces;
  private $tableNamespaces;
  
  public function __construct($appPath = null) {
    parent::__construct($appPath);
    $this->modelIterator = $this->getIterator($this->appPath . "/models");
    $this->setModelNamespaces();
    $this->setModelTables($this->modelNamespaces);
  }

  public function setModelNamespaces() {
    $this->modelNamespaces = $this->getCachedArray(self::MODEL_NAMESPACES);
    if (is_null($this->modelNamespaces)) {
      $this->modelNamespaces = $this->setNamespaceArray($this->modelIterator);
      $this->setCachedArray($this->modelNamespaces, self::MODEL_NAMESPACES);
    } 
  }
  
  public function getTableNamespaces() {
    return $this->tableNamespaces;
  }
  
  public function setModelTables($modelNamespaces) {
    array_walk($modelNamespaces, function($namespace) {
      $tableKey = \core\resolver\Inflector::tableizeModelName($namespace);
      $this->tableNamespaces[$tableKey] = $namespace;
    });
  }
}
