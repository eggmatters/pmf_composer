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

class QueryBase implements IDBConn {

  private $modelClass;
  private $currentTable;
  private $query;
  private $columnsList;
  private $tableAliases;
  private $modelInstances;
  private $fkConstraints;
  private $bindings;
  /**
   *
   * @var core\DBConnector 
   */
  private $dbConn;
  
  public function __construct(DBConnector $connector, \ReflectionClass $modelClass, $eagerLoading = false) {
    $this->modelClass = $modelClass;
    $this->currentTable = \core\resolver\Inflector::tableizeModelName($modelClass->name);
    $this->query = [];
    $this->columnsList = [];
    $this->tableAliases = [];
    $this->bindings = [];
    $this->dbConn = $connector;
    $this->modelInstances = [];
  }
  
  /**
   * 
   * @param variadic $columns
   * @return $this
   */
  public function Select(...$models) {
    $models = (empty($models)) ? [$this->modelClass->getName()] : $models;
    $this->columnsList = array_map('\\core\\connectors\\QueryBase::formatSelectColumns', $models);
    $this->setModelInstances($models);
    $this->query['SELECT'] = "SELECT " . implode(",", $this->columnsList) . " FROM $this->currentTable";
    return $this;
  }
  
  /**
   * 
   * @param \ReflectionClass | string $fromTable
   * @param \ReflectionClass | string  $onTable
   * @param string $foreignKey
   */
  public function LeftJoin($fromTable, $onTable, $foreignKey) {
    $from = (is_a($fromTable, \ReflectionClass::class)) 
      ? Inflector::tableizeModelName($fromTable->name) 
        : Inflector::tableizeModelName($fromTable);
    $on = (is_a($onTable, \ReflectionClass::class)) 
      ? Inflector::tableizeModelName($onTable->name) 
        : Inflector::tableizeModelName($onTable);
    $this->query['JOINS'][] = "LEFT JOIN $from ON "
      . $from . "." . $foreignKey . " = " 
      . $on . "." . $foreignKey;
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
    $queryString .= isset($this->query['JOINS']) ? implode("",$this->query['JOINS']) . " " : "";
    $queryString .= isset($this->query['WHERE']) ? $this->query['WHERE'] . " " : "";
    return $queryString;
  }
  
  public function getBindValues() {
    return $this->bindings;
  }
  
  public static function getCollectionFromResultsSet(array $resultsSet, QueryBase $queryBuilder) {
    $rs = [];
    foreach ($resultsSet as $result) {
      $rs[] = self::getModelFromResultsSet($result, $queryBuilder);
    }
    return $rs;
  }

  public static function getModelFromResultsSet(array $resultsSet, QueryBase $queryBuilder) {
    foreach ($resultsSet as $columnAlias => $value) {
       $queryBuilder->setModelInstanceProperty($columnAlias, $value);
    }
    return $queryBuilder->modelInstances;
  }
  
  private function setModelInstances(array $modelNamespaces) {
    foreach ($modelNamespaces as $namespace) {
      $rf =  new \ReflectionClass($namespace);
      $this->modelInstances[$namespace] = $rf->newInstance();
    }
  }
  
  private function setModelInstanceProperty($columnAlias, $value) {
   $instance = $this->modelInstances[$this->tableAliases[$columnAlias]['namespace']];
   $property = $this->tableAliases[$columnAlias]['property'];
   $instance->{$property} = $value;
  }
  
  private function setBindValues($array) {
    $bindValues = [];
    foreach($array as $value) {
      $bindValues[":$value"] = $value; 
    }
    return $bindValues;
  }
  
  private function setBindValueStrings($array) {
    return array_map(create_function('$str', 'return ":$str";'), $array);
  }
  
  public function foreignKeyConstraints($tablesList) {
    //$tablesList[] = $this->currentTable;
    $tablesListBindStrings = implode(',',$this->setBindValueStrings($tablesList));
    $tablesListBindValues  = $this->setBindValues($tablesList);
    $sql = "SELECT i.TABLE_NAME, k.COLUMN_NAME, k.REFERENCED_TABLE_NAME"
      . " FROM information_schema.TABLE_CONSTRAINTS i"
      . " LEFT JOIN information_schema.KEY_COLUMN_USAGE k ON i.CONSTRAINT_NAME = k.CONSTRAINT_NAME"
      . " WHERE i.CONSTRAINT_TYPE = 'FOREIGN KEY'"
      . " AND i.TABLE_SCHEMA = DATABASE()"
      . " AND k.REFERENCED_TABLE_NAME in ($tablesListBindStrings);";
    if ($this->dbConn->query($sql, $tablesListBindValues)) {
       return $this->dbConn->getResultsSet();
    } else {
      return false;
    }
  }
  
  private function getTableColumns($namespace) {
    $rf = new \ReflectionClass($namespace);
    if ($rf->hasConstant('allowedFields')) {
      return $rf->getConstant('allowedFields');
    }
    $schema = $this->dbConn->getSchema();
    $table = Inflector::tableizeModelName($namespace);
    $sql = "SELECT COLUMN_NAME FROM information_schema.COLUMNS "
      ." WHERE TABLE_SCHEMA = :schema AND TABLE_NAME = :table";
    if ($this->dbConn->query($sql, ['schema' => $schema, 'table' => $table])) {
      return array_column($this->dbConn->getResultsSet(), 'COLUMN_NAME');
    } else {
      return null;
    }
  }
  
  private function formatSelectColumns($namespace) {
    $table  = Inflector::tableizeModelName($namespace);
    $colums = $this->getTableColumns($namespace);
    $namespaceAlias = Inflector::aliasNamepsace($namespace);
    return join(',', array_map(function($column) use ($table, $namespace, $namespaceAlias) {
      $columnAlias = $namespaceAlias . "_$column";
      $this->tableAliases[$columnAlias] = array('namespace' => $namespace, 'property' => $column);
      return "$table.$column as $columnAlias";
    }, $colums));
  }
}
/**
 * Join Table:
 * /posts/1/tags
 *  [0] => Array
        (
            [TABLE_NAME] => posts_tags
            [COLUMN_NAME] => posts_id
            [REFERENCED_TABLE_NAME] => posts
        )

    [1] => Array
        (
            [TABLE_NAME] => posts_tags
            [COLUMN_NAME] => tags_id
            [REFERENCED_TABLE_NAME] => tags
        )
SELECT 
    tags.*
FROM
    -- current table
    tags	
		JOIN
    -- current table = [2] => Array ([REFERENCED_TABLE_NAME] => tag  
  private function stringifyColunns($tableColumns, $tableName) {
    return 
  }
}s)
    posts_tags ON tags.id = posts_tags.tags_id
    -- next table = [2] => Array ([TABLE_NAME] => posts_tags)
		JOIN
	  -- [2] => Array ([TABLE_NAME] => posts_tags)
    -- [1] => Array ([REFERENCED_TABLE_NAME] => posts_             
    posts ON posts.id = posts_tags.posts_id
WHERE
    posts.id = 1;
 * 
 * 
 * Straight Join:
 * /users/2/posts
 *  [0] => Array
        (
            [TABLE_NAME] => posts
            [COLUMN_NAME] => users_id
            [REFERENCED_TABLE_NAME] => users
        )

    [1] => Array
        (
            [TABLE_NAME] => posts_tags
            [COLUMN_NAME] => posts_id
            [REFERENCED_TABLE_NAME] => posts
        )
 * 
SELECT 
    posts.*
FROM
    -- current table
    posts	
		JOIN
    -- current table = [2] => Array ([REFERENCED_TABLE_NAME] => users)
    users ON users.id = posts.users_id
WHERE
    posts.id = 1;
 * Mutli Join:
 * users/1/posts/tags
 *  [0] => Array
        (
            [TABLE_NAME] => posts
            [COLUMN_NAME] => users_id
            [REFERENCED_TABLE_NAME] => users
        )

    [1] => Array
        (
            [TABLE_NAME] => posts_tags
            [COLUMN_NAME] => posts_id
            [REFERENCED_TABLE_NAME] => posts
        )

    [2] => Array
        (
            [TABLE_NAME] => posts_tags
            [COLUMN_NAME] => tags_id
            [REFERENCED_TABLE_NAME] => tags
        )
SELECT 
    tags.*
FROM
    -- current table
    tags	
		JOIN
    -- current table = [2] => Array ([REFERENCED_TABLE_NAME] => tags)
    posts_tags ON tags.id = posts_tags.tags_id
    -- next table = [2] => Array ([TABLE_NAME] => posts_tags)
		JOIN
	-- [2] => Array ([TABLE_NAME] => posts_tags)
    -- [1] => Array ([REFERENCED_TABLE_NAME] => posts_             
    posts ON posts.id = posts_tags.posts_id
    -- 
		JOIN
	-- [0] => Array ([TABLE_NAME] => posts)
    -- [0] => Array ([REFERENCED_TABLE_NAME] => users)
    users on users.id = posts.users_id
WHERE
    users.id = 1;
 */