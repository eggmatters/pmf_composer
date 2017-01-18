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
  const TABLE_COLUMNS = 'table_columns';
  const DB_RELATIONS = 'db_relations';
  /**
   *
   * @var core\connectors\DBConnector 
   */
  private $dbConnector;
  
  private $dbNodes;
  
  /**
   *
   * @var core\connectors\QueryBase 
   */
  private $queryBase;
  
  public function __construct($appPath = null) {
    parent::__construct($appPath);
    $this->dbConnector = new DBConnector(DBConnector::DBCONN);
    $this->queryBase = new QueryBase($this->dbConnector);
    $this->dbNodes = [];
  }
  
  public function setDbNode($tableName) {
    
  }
  
  public function setTableColumns() {
    
  }
  
  private function getConstrainedTables() {
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
    return $this->dbConnector->rawQuery($sql);
  }
}
