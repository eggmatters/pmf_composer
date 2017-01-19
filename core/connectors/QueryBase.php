<?php
namespace core\connectors;
/**
 * This is a basic parser to generate valid sql queries with bound parameters.
 * The only association this class has with the overall framework is the model
 * passed in the constructor.
 * 
 * Additionally, this class is reliant on foreign key constraints established
 * in the database. Calls to join on tables without defined foreign key constraints
 * will not work.
 * 
 * This class relies on the Constraints class to establish where clauses.
 *
 * @author meggers
 */
use core\resolver\Inflector;

use utilities\normalizers\DBNormalizer;

use utilities\cache\DBCache;

class QueryBase {
  /**
   *
   * @var /ReflectionClass 
   */
  private $modelClass;
  /**
   *
   * @var string 
   */
  private $currentTable;
  /**
   *
   * @var array 
   */
  private $query;
  /**
   *
   * @var array 
   */
  private $columnsList;
  /**
   *
   * @var array 
   */
  private $tableAliases;
  /**
   *
   * @var array 
   */
  private $bindings;
  /**
   *
   * @var int 
   */
  private $layout;

  private $dbCache;
  
  private $dbNodes;
  
  public function __construct(\ReflectionClass $modelClass = null
    , DBCache $dbCache = null
    , $layout = DBNormalizer::NESTED_LAYOUT ) 
  {
    $this->modelClass = $modelClass;
    $this->dbCache = $dbCache;
    $this->layout = $layout;
    
    $this->currentTable = (is_null($modelClass)) ?
      "" :
        \core\resolver\Inflector::tableizeModelName($modelClass->name);
    $this->query = [];
    $this->columnsList = [];
    $this->tableAliases = [];
    $this->bindings = [];
    $this->dbNodes = $this->dbCache->getDbNodes();
  }
  
  /**
   * 
   * @param variadic $columns
   * @return $this
   */
  public function Select(...$models) {
    $models = (empty($models)) ? [$this->modelClass->getName()] : $models;
    $this->columnsList = array_map('\\core\\connectors\\QueryBase::formatSelectColumns', $models);
    $this->query['SELECT'] = "SELECT " . implode(",", $this->columnsList) . " FROM $this->currentTable";
    return $this;
  }
  
  /**
   * 
   * @param \ReflectionClass | string $fromTable
   * @param \ReflectionClass | string  $onTable
   * @param string $foreignKey
   */
  public function LeftJoin($fromTable, $onTable, $lvalue, $rvalue = null) {
    $from = (is_a($fromTable, \ReflectionClass::class)) 
      ? Inflector::tableizeModelName($fromTable->name) 
        : Inflector::tableizeModelName($fromTable);
    $on = (is_a($onTable, \ReflectionClass::class)) 
      ? Inflector::tableizeModelName($onTable->name) 
        : Inflector::tableizeModelName($onTable);
    if (is_null($rvalue)) {
      $this->query['JOINS'][] = "LEFT JOIN $from ON "
      . $from . "." . $lvalue . " = " 
      . $on . "." . $lvalue;
    } else {
      $this->query['JOINS'][] = "LEFT JOIN $from ON "
      . $on . "." . $rvalue . " = " 
      . $from . "." . $lvalue;
    }
    return $this;
  }
  
  /**
   * gets the built constraints clause from a constraints instance defined outside of 
   * this method. Sets bound params to constraints to this query.
   * 
   * @param \core\Constraints $constraint
   * @return \core\QueryBase
   */
  public function Where(Constraints $constraint) {
    $constraints = $constraint->getConstraints();
    if (!empty($constraints)) {
      $this->query['WHERE'] = "WHERE " . $constraints;
      $this->bindings = array_merge($this->bindings, $constraint->getBindings());
    }
    return $this;
  }
  
  /**
   * Returns the built query
   * @return string
   */
  public function getSelect() {
    $queryString  = isset($this->query['SELECT']) ? $this->query['SELECT'] ." " : "";
    $queryString .= isset($this->query['JOINS']) ? implode(" ",$this->query['JOINS']) . " " : "";
    $queryString .= isset($this->query['WHERE']) ? $this->query['WHERE'] . " " : "";
    return $queryString;
  }
  
  public function getBindValues() {
    return $this->bindings;
  }
  
  public function getTableAliases() {
    return $this->tableAliases;
  }
  
  private function formatSelectColumns($namespace) {
    $table  = (strpos('Model', $namespace)) ? Inflector::tableizeModelName($namespace) : $namespace;
    $colums = $this->dbNodes[$table]->getColumns();
    $namespaceAlias = Inflector::aliasNamepsace($namespace);
    return join(',', array_map(function($column) use ($table, $namespace, $namespaceAlias) {
      $columnAlias = $namespaceAlias . $column;
      $this->tableAliases[$columnAlias] = array('namespace' => $namespace, 'property' => $column);
      return "$table.$column as $columnAlias";
    }, $colums));
  }
}