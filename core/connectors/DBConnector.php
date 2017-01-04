<?php

namespace core\connectors;

/**
 * Creates and establishes Connections and queries to a database using PDO
 *
 * @author meggers
 */
class DBConnector extends Connector {
  
  protected $host;
  protected $dbName;
  protected $user;
  protected $pass;
  
  private $pdoConn;
  private $stmt;
  private $lastInserted;
  private $resultSet;
  private $numRows;

  public function getAll() {
    $queryBase = new QueryBase($this, $this->modelClass);
    $constraint = new Constraints();
    $sql = $queryBase
      ->Select()
      ->Where($constraint)
      ->getSelect();
    $bindValues = $queryBase->getBindValues();
    if ($this->query($sql, $bindValues)) {
      return $this->getResultsSet();
    }
    return false;  
  }
  
  public function get($id = null) {
    if (is_null($id)) {
      $id = "null";
    }
    $queryBase = new QueryBase($this, $this->modelClass);
    $constraint = new Constraints();
    $constraint->term("id", "=", $id);
    $sql = $queryBase
      ->Select()
      ->Where($constraint)
      ->getSelect();
    $bindValues = $queryBase->getBindValues();
    if ($this->query($sql, $bindValues)) {
      return $this->getResultsSet();
    }
    return false; 
  }
  
  public function create($params) {
    
  }
  
  public function update($id, $params) {
    
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
