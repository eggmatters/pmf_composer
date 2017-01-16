<?php

namespace utilities\normalizers;

use core\connectors\QueryBase;
/**
 * Description of DBNormalizer
 *
 * @author matthewe
 */
class DBNormalizer implements INormalizer {
  
  const NESTED_LAYOUT = 1; // 0b0001
  const EAGER_LOADING = 2; // 0b0010
  const SIDE_BY_SIDE  = 4; // 0b0100
  const LAZY_LOADING  = 8; // 0b1000
  
  private $queryBase;
  private $defaultFormat;
  
  public function __construct(QueryBase $queryBase) {
    $this->queryBase = $queryBase;
    $this->defaultFormat = self::NESTED_LAYOUT | self::EAGER_LOADING;
  }

  public function arrayToModelsCollection(array $resultsCollection, $modelNamespace, $formatter = null) {
    
  }
  
  public function arrayToModel(array $resultsCollection, $modelNamespace, $formatter = null) {
    $formatter = is_null($formatter) ? 0b00 : $formatter;
    // ($formatter & self::NESTED_LAYOUT)
    $tableAliases = $this->queryBuilder->getTableAliases();
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

  public function modelToArray($modelInstance, $formatter = null) {
    
  }

  public function modelToJson($modelInstance, $formatter = null) {
    
  }

}
