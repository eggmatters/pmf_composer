<?php

namespace core\connectors;


/**
 * Creates and establishes Connections and queries to a database using PDO
 *
 * @author meggers
 */
class DBConnector extends Connector {
  
  const NESTED_LAYOUT = 3;
  const SIDE_BY_SIDE  = 5;
  
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
    $queryBuilder = new QueryBase($this, $this->modelClass);
    $constraint = new Constraints();
    $constraint->term("id", "=", $id);
    $sql = $queryBuilder
      ->Select()
      ->Where($constraint)
      ->getSelect();
    $bindValues = $queryBuilder->getBindValues();
    if ($this->query($sql, $bindValues)) {
      return $this->normalizeResultsSet($this->getResultsSet()[0], $queryBuilder);
    }
    return false; 
  }
  
  public function getBy($foreignModel, $foreignKey, $foreignValue, $resultsFormatter = null) {
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
    
    /*
      ->Select($this, $user->getModelNamespace())
      ->LeftJoin($user->getModelNamespace(), $this->getModelNamespace(), "id")
      ->Where($constraints->term('users.id', '=', $user->getMethodArguments()[0]));
    $postsData = $connector->normalizeResultsCollection($connector->executeQuery($qb), $qb);
    $userPosts = PostModel::setCollection($postsData);
     * 
     */
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
  
  public function query($sql, array $bindValues = [], $outputFormat = \PDO::FETCH_ASSOC) {
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
  
  public function normalizeResultsSet(array $resultsSet, $queryBuilder) {
    $tableAliases = $queryBuilder->getTableAliases();
    $resultsCollection = [];
    foreach($resultsSet as $columnAlias => $value) {
      $namespace = $tableAliases[$columnAlias]['namespace'];
      $property  = $tableAliases[$columnAlias]['property'];
      if ($namespace == $this->modelClass->getName()) {
        $resultsCollection[$property] = $value;
      } else {
        $resultsCollection[$namespace][$property] = $value;
      }
    }
    return $resultsCollection;
  }
  
  public function normalizeResultsCollection(array $resultsCollection, $queryBuilder) {
    $modelResults = [];
    foreach($resultsCollection as $resultSet) {
      $modelResults[] = self::normalizeResultsSet($resultSet, $queryBuilder);
    }
    return $modelResults;
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
