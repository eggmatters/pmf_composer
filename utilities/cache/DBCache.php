<?php

namespace utilities\cache;

use core\connectors\QueryBase;
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
  
  /**
   *
   * @var core\connectors\QueryBase 
   */
  private $queryBase;
  
  private $tables;
  
  private $relations;
  
  public function __construct($appPath = null) {
    parent::__construct($appPath);
    $this->dbConnector = new DBConnector(DBConnector::DBCONN);
    $this->queryBase = new QueryBase($this->dbConnector);
    $this->dbNodes = [];
  }
  
  public function setDbNodes() {
    $this->tables = $this->getAllTables();
    $this->relations = $this->getTableRelations();
    foreach($this->tables as $table) {
      $tableColumns = $this->getTableColumns($table);
      
    }
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
    $sql = "SELECT i.TABLE_NAME, k.COLUMN_NAME, k.REFERENCED_TABLE_NAME"
      . " FROM information_schema.TABLE_CONSTRAINTS i"
      . " LEFT JOIN information_schema.KEY_COLUMN_USAGE k ON i.CONSTRAINT_NAME = k.CONSTRAINT_NAME"
      . " WHERE i.CONSTRAINT_TYPE = 'FOREIGN KEY'"
      . " AND i.TABLE_SCHEMA = DATABASE();";
    return $this->dbConnector->rawQuery($sql);
  }
  
  private function getAllTables() {
    $sql = "SELECT TABLE_NAME FROM information_schema.TABLES"
      . " WHERE TABLE_SCHEMA = DATABASE();";
    return array_column($this->dbConnector->rawQuery($sql), 'TABLE_NAME');
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
  
  private function fetchRelation($tableName) {
    $m = array_filter($this->relations, function($row) use ($tableName) {
      if ($row['TABLE_NAME'] == $tableName) {
        $node = $this->getDBNode($tableName);
        $parentNode = $this->getDBNode($row['REFERENCED_TABLE']);
        $parentNode->setChild($tableName, $node);
        $node->setParent($row['REFERENCED_TABLE'], $parentNode);
      }
    });
  }
}
