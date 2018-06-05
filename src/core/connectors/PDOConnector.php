<?php

namespace core\connectors;

/**
 * PDO Connector is a stand-alone instance establishing a connection to a MySQL database.
 * 
 *
 * @author meggers
 */
class PDOConnector {
  private $host;
  private $dbName;
  private $user;
  private $pass;
  
  private $pdoConn;
  private $stmt;
  private $lastInserted;
  private $resultSet;
  private $numRows;
  
  public function __construct($host, $dbName, $user, $pass) {
    $this->host = $host;
    $this->dbName = $dbName;
    $this->user = $user;
    $this->pass = $pass;
  }
  
  public function getConn() {
    return $this->conn();
  }
  
  public function executeQuery(QueryBase $queryBuilder, $limitResultsSet = false) {
    $bindValues = $queryBuilder->getBindValues();
    if ($this->query($queryBuilder->getSelect(), $bindValues)) {
      $results = ($limitResultsSet) ? $this->getResultsSet()[0] ?? [] : $this->getResultsSet();
      return $results;
    }
    return false;
  }
  
  public function rawQuery($sql, array $bindValues = [], $outputFormat = \PDO::FETCH_ASSOC) {
    if ($this->query($sql, $bindValues, $outputFormat)) {
      return $this->getResultsSet();
    }
    return false;
  }
  
  private function query($sql, array $bindValues = [], $outputFormat = \PDO::FETCH_ASSOC) {
    $this->setPdoConn();
    $this->stmt = $this->pdoConn->prepare($sql);
    try {
      $this->stmt->execute($bindValues);
    } catch (\PDOException $e) {
      $errorMessage = "Database error: Code {$e->getCode()}\n"
        . "Message: {$e->getMessage()}";
      //error_log($errorMessage);
      echo($errorMessage);
      unset($this->stmt);
      unset($this->pdoConn);
      $this->resultSet = null;
      return false;
    }
    if ($this->stmt->columnCount() > 0) {
      $this->resultSet = $this->stmt->fetchAll($outputFormat);
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
  
  public function getSchema() {
    return $this->dbName;
  }
  
  private function setPdoConn() {
    try {
      $this->pdoConn = new \PDO("mysql:host=$this->host;dbname=$this->dbName", $this->user, $this->pass);
    } catch (Exception $e) {
      throw new Exception($e->getMessage());
    }
    $this->pdoConn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
  }
}
