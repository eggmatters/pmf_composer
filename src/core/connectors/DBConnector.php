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
    $resultsCollection = $mysql->executeQuery($queryBase);
    return $this->normalizer->arrayToModelsCollection($resultsCollection, $this->modelClass->name, $queryBase->getTableAliases());
  }
  
  public function get($id = "null", $eagerLoading = false, $column = null) {
    $mysql      = $this->getMySql();
    $constraint = new Constraints();
    $idField    = \core\resolver\Inflector::tableizeModelName($this->modelClass->name) . '.' . ($column ?? 'id');
    $constraint->term($idField, "=", $id);
    $queryBase  = new QueryBase($this->modelClass, $this->connectorCache);
    if ($eagerLoading) {
      $this->foreignKeyJoin($queryBase)->Where($constraint);
    } else {
      $queryBase->Select()->Where($constraint);
    }
    $resultsCollection = $mysql->executeQuery($queryBase, true);
    return $this->normalizer->arrayToModel($resultsCollection, $this->modelClass->name, $queryBase->getTableAliases());
  }
  
  public function getBy($column, $value, $eagerLoading = false) {
     return $this->get($value, $eagerLoading, $column);
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
    $resultsCollection = $mysql->executeQuery($queryBase);
    return $this->normalizer->arrayToModelsCollection($resultsCollection, $this->modelClass->name, $queryBase->getTableAliases());
  }
  
  public function getByJoin(\core\ControllerBase $foreignController, $eager = false) {
    $mysql        = $this->getMySql();
    $constraint   = new Constraints();
    $foreignModel = $foreignController->getModelNamespace();
    $foreignValue = $foreignController->getControllerArgs()->getArguments()[0]->value;
    $idField      = \core\resolver\Inflector::tableizeModelName($foreignModel) . '.id';
    $constraint->term($idField, "=", $foreignValue);
    $queryBase    = new QueryBase($this->modelClass, $this->connectorCache);
    $this->joinTableJoin($queryBase, $foreignController->getModelNamespace(), $eager)
            ->Where($constraint);
    $resultsCollection = $mysql->executeQuery($queryBase);
    return $this->normalizer->arrayToModelsCollection($resultsCollection, $this->modelClass->name, $queryBase->getTableAliases());
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
    $parentNode   = $this->connectorCache->getDBNode($currentTable);
    $foreignNode  = $this->connectorCache->getDBNode($foreignTable);
    $this->oneToManyClause($selects, $joins, $parentNode, $foreignNode);
    $this->formatClause($selects, $joins, $qb);
    return $qb;
  }
  
  private function oneToManyClause(&$selects, &$joins, DBNode $parentNode, DBNode $foreignNode) {
    if (!empty($selects)) {
      $selects[] = $foreignNode->getNamespace();
    }
    $joinNodes = array_intersect_key($parentNode->getChildren(), $foreignNode->getChildren());
    if (count($joinNodes) != 1) {
      return;
    }
    $joinNode = array_pop($joinNodes);
    $parentTable = $parentNode->getTableName();
    $foreignTable = $foreignNode->getTableName();
    $joinTable = $joinNode->getTableName();
    $parentFK = $this->connectorCache->getMatchingKey($parentTable, $joinTable);
    $foreignFK = $this->connectorCache->getMatchingKey($foreignTable, $joinTable);
    $joins[] = $this->setJoinsArray(
            $joinTable
            , $parentTable
            , 'id'
            , $parentFK );
    $joins[] = $this->setJoinsArray(
            $foreignTable
            , $joinTable
            , $foreignFK
            , 'id' );
  }
  
  private function manyToOneClause(&$selects, &$joins, DBNode $childNode, $parentNodes) {
    foreach ($parentNodes as $parentNode) {
      /* @var  $parentNode \utilities\cache\DBNode */
      if (!empty($selects)) {
        $selects[]  = $parentNode->getNamespace();
      }
      $childTable   = $childNode->getTableName();
      $parentTable  = $parentNode->getTableName();
      $joins[] = $this->setJoinsArray(
              $parentTable
              , $childTable
              , $this->connectorCache->getMatchingKey($parentTable, $childTable)
              , 'id');
      $parents = $parentNode->getParents();
      if (!empty($parents)) {
        $this->manyToOneClause($selects, $joins, $parentNode, $parents);
      }
    }
  }
  
  private function setJoinsArray($fromTable, $onTable, $lhs, $rhs, $alias = null) {
    return array(
      'fromTable' => $fromTable,
      'onTable'   => $onTable,
      'lhs'       => $lhs,
      'rhs'       => $rhs,
      'alias'     => $alias  
    );
  }
  
  private function formatClause($selects, $joins, QueryBase $qb) {
    $qb->Select($selects);
    foreach ($joins as $join) {
      $qb->LeftJoin($join['fromTable'], $join['onTable'], $join['lhs'], $join['rhs'], $join['alias']);
    }
    return $qb;
  }
  
  private function getMySql() {
    return new PDOConnector($this->host, $this->dbName, $this->user, $this->pass);
  }
  
}
