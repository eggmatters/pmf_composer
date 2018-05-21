<?php

namespace utilities\cache;

use core\connectors\PDOConnector;
/**
 * DBCache -- primary responsibility is to identify and map foreign key 
 * relationships, caching the search patterns. The relationships shall be 
 * modeled as a n-ary ADT tree. 
 * 
 * Each node of the tree shall contain a model namespace (if exists) and
 * the table name. Each node shall point to one or many children. 
 * 
 * DBCache shall additionally cache each column list for tables in the DB,
 * alleviating the need to query it each time.
 *
 * @author meggers
 */
class DBCache extends CacheBase {
  const DB_NODES = 'db_nodes';
  /**
   *
   * @var array - array of anonymous classes. 
   */
  private $dbNodes;
  
  private $tables;
  
  private $relations;
  /**
   *
   * @var \core\connectors\PDOConnector 
   */
  private $pdoConn;
  /**
   *
   * @var ModelCache 
   */
  private $modelCache;
  
  private $modelNamespaces;
  
  use \configurations\schemaConnectorTrait;
  
  public function __construct($appPath = null) {
    parent::__construct($appPath);
    $this->dbNodes = [];
    $this->setPDOConn();
    $this->modelCache = new ModelCache($appPath);
    $this->modelNamespaces = $this->modelCache->getTableNamespaces();
  }
  
  public function setDbNodes() {
    $this->dbNodes = $this->getCachedArray(self::DB_NODES);
    if (!empty($this->dbNodes)) {
      return;
    }
    $this->tables = $this->getAllTables();
    $this->relations = $this->getTableRelations();
    foreach($this->tables as $table) {
      $tableColumns = $this->getTableColumns($table);
      $this->setRelatedNodes($table);
      $this->dbNodes[$table]->setColumns($tableColumns);
    }
  }
  
  public function getDbNodes() {
    $this->dbNodes = $this->getCachedArray(self::DB_NODES);
    if (empty($this->dbNodes)) {
      $this->setDbNodes();
      $this->setCachedArray($this->dbNodes, self::DB_NODES);
    }
    return $this->dbNodes;
  }
  /**
   * 
   * @param string $tableName
   * @return DBNode
   */
  public function getDBNode($tableName) {
    if (!isset($this->dbNodes[$tableName])) {
      $modelNamespace = isset($this->modelNamespaces[$tableName]) ?
        $this->modelNamespaces[$tableName] : null;
      $this->dbNodes[$tableName] = new DBNode($tableName, $modelNamespace);
    } 
    return $this->dbNodes[$tableName];
  }
  
  public function getMatchingKey($parentTable, $childTable) {
    foreach($this->relations as $relation) {
      if ($relation['TABLE_NAME'] == $childTable && 
          $relation['REFERENCED_TABLE_NAME'] == $parentTable) {
        return $relation['COLUMN_NAME'];
      }
    }
    return null;
  }
  
  private function getTableRelations() {
    $sql = "SELECT i.TABLE_NAME, k.COLUMN_NAME, k.REFERENCED_TABLE_NAME"
      . " FROM information_schema.TABLE_CONSTRAINTS i"
      . " LEFT JOIN information_schema.KEY_COLUMN_USAGE k ON i.CONSTRAINT_NAME = k.CONSTRAINT_NAME"
      . " WHERE i.CONSTRAINT_TYPE = 'FOREIGN KEY'"
      . " AND i.TABLE_SCHEMA = DATABASE();";
    return $this->pdoConn->rawQuery($sql);
  }
  
  private function getAllTables() {
    $sql = "SELECT TABLE_NAME FROM information_schema.TABLES"
      . " WHERE TABLE_SCHEMA = DATABASE();";
    return array_column($this->pdoConn->rawQuery($sql), 'TABLE_NAME');
  }
  
  private function getTableColumns($table) {
    $sql = "SELECT COLUMN_NAME FROM information_schema.COLUMNS "
      ." WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = :table";
    $resultsSet = $this->pdoConn->rawQuery($sql, ['table' => $table]);
    if ($resultsSet) {
      return array_column($resultsSet, 'COLUMN_NAME');
    } else {
      return null;
    }
  }
  
  private function setRelatedNodes($tableName) {
    $iterator = new \core\SimpleIterator($this->relations);
    $row = $iterator->current();
    $node = $this->getDBNode($tableName);
    while ($iterator->hasNext()) {
      $parentNode = $this->getDBNode($row['REFERENCED_TABLE_NAME']);
      $childNode = $this->getDBNode($row['TABLE_NAME']);
      if ($row['TABLE_NAME'] == $tableName) {
        $node->setParent($row['REFERENCED_TABLE_NAME'], $parentNode);
      } else if ($row['REFERENCED_TABLE_NAME'] == $tableName) {
        $node->setChild($row['TABLE_NAME'], $childNode);
      }
      $row = $iterator->next();
    }
  }
  
  private function setPDOConn() {
    $credentials = self::getConnectorConfiguration()['ConnectorConfig'];
    $this->pdoConn =  new PDOConnector($credentials['host'], $credentials['dbName'], $credentials['user'], $credentials['pass']);
  }
}
