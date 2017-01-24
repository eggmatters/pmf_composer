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
  
  public function getAll($eagerLoading = false) {
    $mysql      = $this->getMySql();
    $constraint = new Constraints();
    $queryBase  = new QueryBase($this->modelClass, $this->connectorCache);
    if ($eagerLoading) {
      $this->foreignKeyJoin($queryBase, $eagerLoading)->Where($constraint);
    } else {
      $queryBase->Select()->Where($constraint);
    }
    return $mysql->executeQuery($queryBase);
  }
  
  public function get($id = "null", $eagerLoading = false) {
    $mysql      = $this->getMySql();
    $constraint = new Constraints();
    $idField    = \core\resolver\Inflector::tableizeModelName($this->modelClass->name) . '.id';
    $constraint->term($idField, "=", $id);
    $queryBase  = new QueryBase($this->modelClass, $this->connectorCache);
    if ($eagerLoading) {
      $this->foreignKeyJoin($queryBase)->Where($constraint);
    } else {
      $queryBase->Select()->Where($constraint);
    }
    return $mysql->executeQuery($queryBase, true);
  }
  
  public function getByParent(\core\ControllerBase $foreignController, $eager = false) {
    if (!isset($foreignController->getControllerArgs()->getArguments()[0]->value)) {
      return null;
    }
    $mysql        = $this->getMySql();
    $constraint   = new Constraints();
    $foreignModel = $foreignController->getModelNamespace();
    $foreignValue = $foreignController->getControllerArgs()->getArguments()[0]->value;
    $idField      = \core\resolver\Inflector::tableizeModelName($foreignModel) . '.id';
    $constraint->term($idField, "=", $foreignValue);
    $queryBase    = new QueryBase($this->modelClass, $this->connectorCache);
    $this->foreignKeyJoin($queryBase, ($eager) ? [$foreignModel] : null)->Where($constraint);
    return $mysql->executeQuery($queryBase);
  }
  
  public function getByJoin(\core\ControllerBase $foreignController, $eager = false) {
    $mysql        = $this->getMySql();
    $constraint   = new Constraints();
    $foreignModel = $foreignController->getModelNamespace();
    $foreignValue = $foreignController->getControllerArgs()->getArguments()[0]->value;
    $idField      = \core\resolver\Inflector::tableizeModelName($foreignModel) . '.id';
    $constraint->term($idField, "=", $foreignValue);
    $queryBase    = new QueryBase($this->modelClass, $this->connectorCache);
    
    
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
  
  private function foreignKeyJoin(QueryBase $qb, $eager = false) {
    $currentTable = \core\resolver\Inflector::tableizeModelName($this->modelClass->getName());
    $selects      = ($eager) ? [$this->modelClass->name] : [];
    $joins        = [];
    $currentNode  = $this->connectorCache->getDBNode($currentTable);
    $parents      = $currentNode->getParents();
    $this->manyToOneClause($selects, $joins, $currentNode, $parents);
    $this->formatClause($selects, $joins, $qb);
    return $qb;
  }
  
  private function joinTableJoin(QueryBase $qb, $foreignNamespace, $eager = false) {
    $currentTable = \core\resolver\Inflector::tableizeModelName($this->modelClass->getName());
    $foreignTable = \core\resolver\Inflector::tableizeModelName($foreignNamespace);
    $selects      = ($eager) ? [$this->modelClass->name] : [];
    $joins        = [];
    $currentNode  = $this->connectorCache->getDBNode($currentTable);
    $foreignNode  = $this->connectorCache->getDBNode($foreignTable);
  }
  
  private function oneToManyClause(&$selects, &$joins, DBNode $parentNode, DBNode $childNode) {
    
  }
  
  private function manyToOneClause(&$selects, &$joins, DBNode $childNode, $parentNodes) {
    foreach ($parentNodes as $parentNode) {
      /* @var  $parentNode \utilities\cache\DBNode */
      if (!empty($selects)) {
        $selects[]    = $parentNode->getNamespace();
      }
      $childTable   = $childNode->getTableName();
      $parentTable  = $parentNode->getTableName();
      $joins[] = array(
        'fromTable' => $parentTable,
        'onTable'   => $childTable,
        'lhs'       => $this->connectorCache->getMatchingKey($parentTable, $childTable),
        'rhs'       => 'id'
      );
      $parents = $parentNode->getParents();
      if (!empty($parents)) {
        $this->manyToOneClause($selects, $joins, $parentNode, $parents);
      }
    }
  }
  
  private function formatClause($selects, $joins, QueryBase $qb) {
    $qb->Select($selects);
    foreach ($joins as $join) {
      $qb->LeftJoin($join['fromTable'], $join['onTable'], $join['lhs'], $join['rhs']);
    }
    return $qb;
  }
  
  private function getMySql() {
    return new PDOConnector($this->host, $this->dbName, $this->user, $this->pass);
  }
  
}
