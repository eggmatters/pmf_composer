<?php

namespace core;

/**
 * Creates and establishes Connections and queries to a database using PDO
 *
 * @author meggers
 */
class DBConnector extends Connector {
    
  private $pdoConn;
  private $stmt;
  private $lastInserted;
  private $resultSet;
  private $numRows;

  public function getAll() {
    
  }
  
  public function get($id = null) { 
    $request = CoreApp::getRequest();
    $selectTable = QueryBase::tableizeModelName($this->modelClass);
    $parsedResources = $this->parseResources($request->getResourceArray());
    $queryBase = new QueryBase($this->modelClass);
    $constraint = new Constraints();
    if (empty($id)) {
      $resourceArray = $request->getResourceArray();
      $id = $resourceArray[count($resourceArray) - 1];
    }
    
    $queryBase->Select();
    if (!empty($parsedResources['joins'])) {
      $joinsArray = array_map("core\Inflector::tableize", $parsedResources['joins']);
      $queryBase->Join($joinsArray);
    }
    $constraint->term("$selectTable" . ".id", "=", $id);
    if (!empty($parsedResources['constraints'])) {
      foreach ($parsedResources['constraints'] as $kv) {
        $table = Inflector::tableize($kv->resource) . ".id";
        $value = $kv->value;
        $constraint->andTerm($table, "=", $value);
      }
    }
    $queryBase->Where($constraint);
    $sql = $queryBase->getSelect();
    $bindValues = $queryBase->getBindValues();
    if ($this->query($sql, $bindValues)) {
      return $this->getResultsSet();
    }
    return false;
  }
  
  public function create($params) {
    
  }
  
  public function update($params) {
    
  }
  
  public function delete($params = null) {
    
  }
  
  public function query($sql, array $bindValues = []) {
    $this->conn();
    $this->stmt = $this->pdoConn->prepare($sql);
    try {
      $this->stmt->execute($bindValues);
    } catch (\PDOException $e) {
      $errorMessage = "Database error: Code {$e->getCode()}\n"
        . "Message: {$e->getMessage()}";
      error_log($errorMessage);
      return false;
    }
    if ($this->stmt->columnCount() > 0) {
      $this->resultSet = $this->stmt->fetchAll(\PDO::FETCH_ASSOC);
    } else {
      $this->lastInserted = $this->pdoConn->lastInsertId();
      $this->numRows = $this->stmt->rowCount();
    }
    unset($this->stmt);
    unset($this->pdoConn);
    return true;
  }
  
  public function getResultsSet() {
    if (isset($this->resultSet)) {
      return $this->resultSet;
    } else {
      return null;
    }
  }
  
  public function getLastInsertId() {
    if (isset($this->lastInserted)) {
      return $this->lastInserted;
    } else {
      return null;
    }
  }
  
  public function getNumRows() {
    if (isset($this->numRows)) {
      return $this->numRows;
    } else {
      return null;
    }
  }
  
  public function flattenResultSet() {

  }
  
  private function conn() {
    try {
      $this->pdoConn = new \PDO("mysql:host=$this->host;dbname=$this->dbName", $this->user, $this->pass);
    } catch (Exception $e) {
      throw new Exception($e->getMessage());
    }
    $this->pdoConn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
  }
}
