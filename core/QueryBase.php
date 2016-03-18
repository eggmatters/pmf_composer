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
  
  public function __construct() {
    $this->query = [];
    $this->columnsList = [];
  }
  
  public function Select($columns = null) {
    if (!is_null($columns) && is_array($columns)) {
      $this->columnsList = $columns;
    } else if (!is_null($columns) && is_string($columns)) {
      $this->columnsList = $columns;
    }
    $this->query['select'] = "SELECT";
    return $this;
  }
  
  public function From($tables = null) {
    
  }
  
  public function Join($id, $resource) {
    $queryBase = [];
    if (is_string($query)) {
      $queryBase['base'] = $query;
    }
  }
  
  private function aliasColumns($columns) {
    
    $dbconn = new DBConnector();
  }
}
