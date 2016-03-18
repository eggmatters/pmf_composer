<?php
namespace core;
/**
 * Description of QueryBase
 *
 * @author meggers
 */
class QueryBase {

  private $currentModel;
  private $query;
  private $columnsList;
  private $tablesList;
  /**
   *
   * @var core\DBConnector 
   */
  private $dbConn;
  
  public function __construct($currentModel = null) {
    require_once dirname(__DIR__) . '/configurations/ModelMapper.php';
    $reflectionClass = new \ReflectionClass($currentModel);
    $this->currentModel = Inflector::tableize($reflectionClass->getName());
    $this->query = [];
    $this->columnsList = [];
    $this->tablesList = [];
    $this->dbConn = new DBConnector($schemaConnector);
  }
  
  public function Select($columns = null) {
    $columnsList = [];
    if (is_array($columns)) {
      $columnsList = $columns;
    } else if (is_string($columns)) {
      $columnsList = explode(',', $columns);
    }
    
    $this->columnsList = array_map('core\Inflector::underscore', $columnsList);
    if (count($this->columnsList) > 0 ) {
      $this->query['select'] = "SELECT " . implode($columnsList) . " FROM $this->currentModel";
    } else {
      $this->query['select'] = "SELECT * FROM $this->currentModel";
    }
    return $this;
  }
  
  public function Join($tables = null) {
    $tablesList = [];
    if (is_array($tables)) {
      $tablesList = $tables;
    } else if (is_string($tables)) {
      $tablesList = explode(',', $tables);
    }
    $this->tablesList = array_map('core\Inflector::tableize', $tablesList);
    
    return $this;
  }
  
  public function getQuery() {
    return $this->query;
  }
  
  private function aliasColumns() {
    $columnsValues   = implode(',', $this->setBindValueStrings($this->columnsList));
    $columnBindings = $this->setBindValues($this->columnsList);
    $sql = "SELECT `TABLE_NAME`, `COLUMN_NAME` FROM information_schema.COLUMNS";
    $sql .= " WHERE `COLUMN_NAME` IN ($columnsValues) AND TABLE_SCHEMA = 'pmf_db'";
    $this->dbConn->conn();
    if ($this->dbConn->query($sql, $columnBindings)) {
      return $this->dbConn->getResultsSet();
    } else {
      return false;
    }
  }
  
  private function setBindValues($array) {
    $bindValues = [];
    foreach($array as $value) {
      $bindValues[":$value"] = $value; 
    }
    return $bindValues;
  }
  
  private function setBindValueStrings($array) {
    return array_map(create_function('$str', 'return ":$str";'), $array);
  }
}
/*
SELECT 
		i.TABLE_NAME,
        k.COLUMN_NAME
	FROM
		information_schema.TABLE_CONSTRAINTS i
			LEFT JOIN
		information_schema.KEY_COLUMN_USAGE k ON i.CONSTRAINT_NAME = k.CONSTRAINT_NAME
	WHERE
		i.CONSTRAINT_TYPE = 'FOREIGN KEY'
			AND i.TABLE_SCHEMA = DATABASE()
			AND k.REFERENCED_TABLE_NAME = 'users';
 * 
 */