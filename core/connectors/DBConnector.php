<?php

namespace core\connectors;

use utilities\normalizers\INormalizer;
use utilities\normalizers\DBNormalizer;
use utilities\cache\DBNode;
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
  
  public function __construct(int $conntype, \ReflectionClass $modelClass = null, \utilities\cache\ICache $connectorCache = null) {
    parent::__construct($conntype, $modelClass, $connectorCache);
  }
  
  public function getAll($eagerLoading = false) {
    $mysql = $this->getMySql();
    $constraint = new Constraints();
    $queryBase = new QueryBase($this->modelClass, $this->connectorCache);
    if ($eagerLoading) {
      $this->eagerFetch($queryBase)->Where($constraint);
    } else {
      $queryBase->Select()->Where($constraint);
    }
    return $mysql->executeQuery($queryBase);
  }
  
  public function get($id = "null", $eagerLoading = false) {
    $mysql = $this->getMySql();
    $constraint = new Constraints();
    $idField = \core\resolver\Inflector::tableizeModelName($this->modelClass->name) . '.id';
    $constraint->term($idField, "=", $id);
    $queryBase = new QueryBase($this->modelClass, $this->connectorCache);
    if ($eagerLoading) {
      $this->eagerFetch($queryBase)->Where($constraint);
    } else {
      $queryBase->Select()->Where($constraint);
    }
    return $mysql->executeQuery($queryBase, true);
  }
  
  public function getBy(\core\ControllerBase $foreignController, $eager = false) {
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
  
  private function eagerFetch(QueryBase $qb) {
    $currentTable = \core\resolver\Inflector::tableizeModelName($this->modelClass->getName());
    /* @var  $currentNode \utilities\cache\DBNode */
    $currentNode = $this->connectorCache->getDBNode($currentTable);
    $parents = $currentNode->getParents();
    $selects = [$this->modelClass->getName()];
    $joins = [];
    $this->setEagerSelectsAndJoins($selects, $joins, $parents, $currentNode);
    $this->formatEagerSelect($selects, $joins, $qb);
    return $qb;
  }
  
  private function setEagerSelectsAndJoins(&$selects, &$joins, $parentNodes, DBNode $childNode) {
    foreach ($parentNodes as $parentNode) {
      /* @var  $parentNode \utilities\cache\DBNode */
      $selects[] = $parentNode->getNamespace();
      $childTable = $childNode->getTableName();
      $parentTable = $parentNode->getTableName();
      $joins[] = array(
        'fromTable' => $parentTable,
        'onTable'   => $childTable,
        'lhs'       => $this->connectorCache->getParentKey($parentTable, $childTable),  
        'rhs'       => 'id'
      );
      $parents = $parentNode->getParents();
      if (!empty($parents)) {
        $this->setEagerSelectsAndJoins($selects, $joins, $parents);
      }
    }
  }
  
  private function formatEagerSelect($selects, $joins, QueryBase $qb) {
    $qb->Select($selects);
    foreach ($joins as $join) {
      $qb->LeftJoin($join['fromTable'], $join['onTable'], $join['lhs'], $join['rhs']);
    }
    return $qb;
  }
}
