<?php

namespace utilities\cache;

use core\connectors\DBConnector;
/**
 * DBCache -- primary responsibility is to identify and map foreign key 
 * relationships, caching the search patterns. The relationships shall be 
 * modeleled as a n-ary ADT tree. 
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
   * @var core\connectors\DBConnector 
   */
  private $dbConnector;
  /**
   *
   * @var array - array of anonymous classes. 
   */
  private $dbNodes;
  
  private $tables;
  
  private $relations;
  
  public function __construct($appPath = null, DBConnector $connector = null) {
    parent::__construct($appPath);
    $this->dbConnector = $connector;
    $this->dbNodes = [];
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
  private function getDBNode($tableName) {
    if (!isset($this->dbNodes[$tableName])) {
      $this->dbNodes[$tableName] = new DBNode($tableName);
    } 
    return $this->dbNodes[$tableName];
  }
  
  private function getTableRelations() {
    $schema = $this->dbConnector->getSchema();
    $sql = "SELECT i.TABLE_NAME, k.COLUMN_NAME, k.REFERENCED_TABLE_NAME"
      . " FROM information_schema.TABLE_CONSTRAINTS i"
      . " LEFT JOIN information_schema.KEY_COLUMN_USAGE k ON i.CONSTRAINT_NAME = k.CONSTRAINT_NAME"
      . " WHERE i.CONSTRAINT_TYPE = 'FOREIGN KEY'"
      . " AND i.TABLE_SCHEMA = :schema;";
    return $this->dbConnector->rawQuery($sql, array('schema' => $schema));
  }
  
  private function getAllTables() {
    $schema = $this->dbConnector->getSchema();
    $sql = "SELECT TABLE_NAME FROM information_schema.TABLES"
      . " WHERE TABLE_SCHEMA = :schema;";
    return array_column($this->dbConnector->rawQuery($sql, array('schema' => $schema)), 'TABLE_NAME');
  }
  
  private function getTableColumns($table) {
    $sql = "SELECT COLUMN_NAME FROM information_schema.COLUMNS "
      ." WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = :table";
    if ($this->dbConn->query($sql, ['table' => $table])) {
      return array_column($this->dbConn->getResultsSet(), 'COLUMN_NAME');
    } else {
      return null;
    }
  }
  
  private function setRelatedNodes($tableName) {
    $iterator = new \core\SimpleIterator($this->relations);
    $row = $iterator->current();
    while ($iterator->hasNext()) {
      if ($row['TABLE_NAME'] == $tableName) {
        $node = $this->getDBNode($tableName);
        $parentNode = $this->getDBNode($row['REFERENCED_TABLE']);
        $parentNode->setChild($tableName, $node);
        $node->setParent($row['REFERENCED_TABLE'], $parentNode);
      } else if ($row['REFERENCED_TABLE_NAME'] == $tableName) {
        $node = $this->getDBNode($tableName);
        $childNode = $this->getDBNode($row['TABLE_NAME']);
        $node->setChild($row['TABLE_NAME'], $childNode);
      }
      $iterator->next();
    }
  }
}
