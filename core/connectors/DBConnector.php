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
  
  private $dbNodes;
  
  public function getAll() {
    $mysql = $this->getMySql();
    $constraint = new Constraints();
    $queryBase = (new QueryBase($this, $this->modelClass))
      ->Select()
      ->Where($constraint)
      ->getSelect();
    return $mysql->executeQuery($queryBase);
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
    $qb = new QueryBase($this, $this->modelClass);
      $qb->Select($this->modelClass->getName(), $foreignModel)
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
  public function getQueryBase() {
    return new QueryBase($this, $this->modelClass);
  }
  
  public function normalizeResultsSet(array $resultsSet, INormalizer $dbNormalizer) {
    return $dbNormalizer->arrayToModel($resultsSet);
  }

  public function normalizeResultsCollection(array $resultsCollection, INormalizer $normalizer) {
    
  }
  
  private function getMySql() {
    return new PDOConnector($this->host, $this->dbName, $this->user, $this->pass);
  }
}
