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
  
  }

  public function setModelNamespaces() {
    $this->modelNamespaces = $this->getCachedArray(self::MODEL_NAMESPACES);
    if (is_null($this->modelNamespaces)) {
      $this->modelNamespaces = $this->setNamespaceArray($this->modelIterator);
      $this->setCachedArray($this->modelNamespaces, self::CONTROLLER_NAMESPACES);
    } 
  }
  
  public function getTableNamespaces() {
    return $this->modelNamespaces;
  }
  
  public function setModelTables($modelNamespaces) {
    $tables = array_walk($modelNamespaces, $callback)
  }
}
