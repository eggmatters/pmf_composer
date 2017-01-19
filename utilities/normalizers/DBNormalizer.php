<?php

namespace utilities\normalizers;

use core\connectors\QueryBase;
/**
 * Description of DBNormalizer
 *
 * @author matthewe
 */
class DBNormalizer implements INormalizer {
  private $queryBase;
  private $defaultFormat;
  
  public function __construct(QueryBase $queryBase) {
    $this->queryBase = $queryBase;
    $this->defaultFormat = self::NESTED_LAYOUT | self::EAGER_LOADING;
  }

  public function arrayToModelsCollection(array $resultsCollection, $modelNamespace = null, $formatter = null) {
    
  }
  
  public function arrayToModel(array $resultsSet, $modelNamespace = null, $formatter = null) {
    
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
