<?php
namespace core;
/**
 * This is a basic parser to generate valid sql queries with bound parameters.
 * The only association this class has with the overall framework is the model
 * passed in the constructor.
 * 
 * Additionally, this class is reliant on foreign key constraints established
 * in the databse. Calls to join on tables without defined foreign key constraints
 * will not work.
 * 
 * This class relies on the Constraints class to establish where clauses.
 *
 * @author meggers
 */
class QueryBase {

  private $currentTable;
  private $query;
  private $columnsList;
  private $tablesList;
  private $fkConstraints;
  private $bindings;
  /**
   *
   * @var core\DBConnector 
   */
  private $dbConn;
  
  /**
   * Constructor accepts the class path of the model it is selecting from
   * @param string $currentModel
   */
  public function __construct($currentModel) {
    global $schemaConnector;
    $reflectionClass = new \ReflectionClass($currentModel);
    $this->currentTable = $this->tableizeModelName($reflectionClass->getName());
    $this->query = [];
    $this->columnsList = [];
    $this->tablesList = [];
    $this->bindings = [];
    $this->dbConn = new DBConnector($schemaConnector);
  }
  /**
   * Columns list may be either an array or comma seperated string.
   * Columns list must be in table_name.column_name format.
   * @param array|string $columns
   * @return \core\QueryBase
   */
  public function Select($columns = null) {
    $columnsList = [];
    if (is_array($columns)) {
      $columnsList = $columns;
    } else if (is_string($columns)) {
      $columnsList = explode(',', $columns);
    }
    
    $this->columnsList = array_map('core\Inflector::underscore', $columnsList);
    if (count($this->columnsList) > 0 ) {
      $this->query['SELECT'] = "SELECT " . implode(",", $this->columnsList) . " FROM $this->currentTable";
    } else {
      $this->query['SELECT'] = "SELECT * FROM $this->currentTable";
    }
    return $this;
  }
  /**
   * Tables will be a valid list of tables with foreign key relations
   * on the currentTable (model).
   * 
   * Tables may be an array or comma seperated string
   * 
   * @param array | string $tables
   * @return \core\QueryBase
   */
  public function Join($tables = null) {
    $tablesList = [];
    if (is_array($tables)) {
      $tablesList = $tables;
    } else if (is_string($tables)) {
      $tablesList = explode(',', $tables);
    }
    $this->tablesList = array_map('core\Inflector::tableize', $tablesList);
    $this->fkConstraints = $this->foreignKeyConstraints($tablesList);
    foreach ($this->fkConstraints as $constraint) {
      if (in_array($constraint['REFERENCED_TABLE_NAME'], $tablesList)) {
        $this->query['JOINS'][] = "JOIN {$constraint['REFERENCED_TABLE_NAME']}"
          . " ON {$constraint['TABLE_NAME']}.{$constraint['COLUMN_NAME']} ="
          . " {$constraint['REFERENCED_TABLE_NAME']}.id ";
      }
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
    $this->query['WHERE'] = "WHERE " . $constraint->getConstraints();
    $this->bindings = array_merge($this->bindings, $constraint->getBindings());
    return $this;
  }
  
  /**
   * Returns the built query
   * @return string
   */
  public function getSelect() {
    $queryString =  isset($this->query['SELECT']) ? $this->query['SELECT'] ." " : "";
    $queryString .= isset($this->query['JOINS']) ? implode("",$this->query['JOINS']) . " " : "";
    $queryString .= isset($this->query['WHERE'] ) ? $this->query['WHERE'] . " " : "";
    return $queryString;
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
  
  private function tableizeModelName($modelName) {
    $className = preg_replace("/.\w.*\\\([A-Za-z].*)/", "$1", $modelName);
    $classBase = str_replace('Model', '', $className);
    return Inflector::tableize($classBase);
  }
  
  private function foreignKeyConstraints($tablesList) {
    $tablesList[] = $this->currentTable;
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
}


/*
SELECT 
		i.TABLE_NAME,
        k.COLUMN_NAME
	FROM
		information_schema.TABLE_CONSTRAINTS i
			LEFT JOIN
		information_schema.KEY_COLUMN_USAGE k ON i.CONSTRAINT_NAME = k.CONSTRAINT_NAME
	WHERE
		i.CONSTRAINT_TYPE = 'FOREIGN KEY'
			AND i.TABLE_SCHEMA = DATABASE()
			AND k.REFERENCED_TABLE_NAME = 'users';
 * 
 CREATE TABLE `pmf_db`.`tests` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '',
  `test_data` VARCHAR(45) NULL COMMENT '',
  `test_value` VARCHAR(45) NULL COMMENT '',
  `user_id` INT NOT NULL COMMENT '',
  PRIMARY KEY (`id`)  COMMENT '');
 * 
 CREATE TABLE `pmf_db`.`users` (
  `id` INT NOT NULL AUTO_INCREMENT COMMENT '',
  `username` VARCHAR(45) NOT NULL COMMENT '',
  `user_role` VARCHAR(45) NOT NULL COMMENT '',
  `everything` VARCHAR(45) BINARY NOT NULL COMMENT '',
  `number` INT UNSIGNED ZEROFILL NOT NULL COMMENT '',
  PRIMARY KEY (`id`)  COMMENT '',
  UNIQUE INDEX `everything_UNIQUE` (`everything` ASC)  COMMENT '',
  UNIQUE INDEX `number_UNIQUE` (`number` ASC)  COMMENT '');
 * 
 ALTER TABLE `pmf_db`.`tests` 
ADD INDEX `fk_tests_users_idx` (`user_id` ASC)  COMMENT '';
ALTER TABLE `pmf_db`.`tests` 
ADD CONSTRAINT `fk_tests_users`
  FOREIGN KEY (`user_id`)
  REFERENCES `pmf_db`.`users` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;
 * 
 
 * 
 */