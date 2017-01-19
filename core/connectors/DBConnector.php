<?php

namespace core\connectors;

use utilities\normalizers\INormalizer;
use utilities\normalizers\DBNormalizer;
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
  private $dbNodes;

  public function __construct(int $conntype, \ReflectionClass $modelClass = null) {
    parent::__construct($conntype, $modelClass);
    $dbCache = new \utilities\cache\DBCache("", $this);
    $this->dbNodes = $dbCache->getDbNodes();
  }
  
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
    $queryBuilder = new QueryBase($this, $this->modelClass);
    $constraint = new Constraints();
    $constraint->term("id", "=", $id);
    $sql = $queryBuilder
      ->Select()
      ->Where($constraint)
      ->getSelect();
    $bindValues = $queryBuilder->getBindValues();
    if ($this->query($sql, $bindValues)) {
      $dbNormalizer = new DBNormalizer($queryBuilder);
      return $this->normalizeResultsSet($this->getResultsSet()[0], $dbNormalizer);
    }
    return false; 
  }
  
  public function getBy(\core\ControllerBase $foreignController, $foreignKey, $resultsFormatter = self::NESTED_LAYOUT) {
    $foreignModel = $foreignController->getModelNamespace();
    $foreignValue = $foreignController->getControllerArgs()->getArguments()[0]->value;
    $lhs = \core\resolver\Inflector::tableizeModelName($foreignModel) . ".$foreignKey";
    $contstraints = new Constraints();
    $qb = $this->buildQuery()
      ->Select($this->modelClass->getName(), $foreignModel)
      ->LeftJoin($foreignModel, $this->modelClass->getName(), $foreignKey)
      ->Where($contstraints->term($lhs, "=", $foreignValue));
    $sql = $qb->getSelect();
    $bindValues = $qb->getBindValues();
    if ($this->query($sql, $bindValues)) {
      return $this->normalizeResultsCollection($this->getResultsSet(), $qb);
    }
  }
  
  public function create($params) {
    
  }
  
  public function update($id, $params) {
    
  }
  
  public function delete($params = null) {
    
  }
  /**
   * 
   * @return \core\connectors\QueryBase
   */
  public function buildQuery() {
    return new QueryBase($this, $this->modelClass);
  }
  
  public function executeQuery(QueryBase $queryBuilder) {
    $bindValues = $queryBuilder->getBindValues();
    if ($this->query($queryBuilder->getSelect(), $bindValues)) {
      return $this->getResultsSet();
    }
    return false;
  }
  
  public function rawQuery($sql, array $bindValues = [], $outputFormat = \PDO::FETCH_ASSOC) {
    if ($this->query($sql, $bindValues, $outputFormat)) {
      return $this->getResultsSet();
    }
    return false;
  }
  public function query($sql, array $bindValues = [], $outputFormat = \PDO::FETCH_ASSOC) {
    $this->conn();
    $this->stmt = $this->pdoConn->prepare($sql);
    try {
      $this->stmt->execute($bindValues);
    } catch (\PDOException $e) {
      $errorMessage = "Database error: Code {$e->getCode()}\n"
        . "Message: {$e->getMessage()}";
      error_log($errorMessage);
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
  
  public function normalizeResultsSet(array $resultsSet, INormalizer $dbNormalizer) {
    return $dbNormalizer->arrayToModel($resultsSet);
  }
  
//  public function normalizeResultsCollection(array $resultsCollection, $queryBuilder) {
//    $modelResults = [];
//    foreach($resultsCollection as $resultSet) {
//      $modelResults[] = self::normalizeResultsSet($resultSet, $queryBuilder);
//    }
//    return $modelResults;
//  }
  
  private function conn() {
    try {
      $this->pdoConn = new \PDO("mysql:host=$this->host;dbname=$this->dbName", $this->user, $this->pass);
    } catch (Exception $e) {
      throw new Exception($e->getMessage());
    }
    $this->pdoConn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
  }

  public function normalizeResultsCollection(array $resultsCollection, INormalizer $normalizer) {
    
  }

}
