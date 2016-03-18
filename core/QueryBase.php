<?php
namespace core;
/**
 * Description of QueryBase
 *
 * @author meggers
 */
class QueryBase {

  private $query;
  private $columnsList;
  private $tablesList;
  /**
   *
   * @var core\DBConnector 
   */
  private $dbConn;
  
  public function __construct() {
    require_once dirname(__DIR__) . '/configurations/ModelMapper.php';
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
    $this->columnsList = $this->tableizeArray($columnsList);
    $this->query['select'] = "SELECT";
    return $this;
  }
  
  public function From($tables) {
    $tablesList = [];
    if (is_array($tables)) {
      $tablesList = $tables;
    } else if (is_string($tables)) {
      $tablesList = explode(',', $tables);
    }
    $this->tablesList = $this->tableizeArray($tablesList);
    $this->query['select-criteria'] = $this->aliasColumns();
    return $this;
  }
  
  public function getQuery() {
    return $this->query;
  }
  
  private function tableizeArray($arrayNames) {
    $retArray = [];
    foreach ($arrayNames as $name) {
      $retArray[] = Inflector::tableize($name);
    }
    return $retArray;
  }
  
  private function aliasColumns() {
    $tablesValues   = implode(',', $this->setBindValueStrings($this->tablesList));
    $tablesBindings = $this->setBindValues($this->tablesList);
    $columnsValues   = implode(',', $this->setBindValueStrings($this->columnsList));
    $columnBindings = $this->setBindValues($this->columnsList);
    $sql = "SELECT `TABLE_NAME`, `COLUMN_NAME` FROM information_schema";
    $sql .= " WHERE `TABLE_NAME` IN ($tablesValues) AND `COLUMN_NAME IN ($columnsValues)";
    $bindArray = array_merge($tablesBindings, $columnBindings);
    $this->dbConn->conn();
    return $this->dbConn->query($sql, $bindArray);    
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
