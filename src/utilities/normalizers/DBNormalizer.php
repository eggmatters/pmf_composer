<?php

namespace utilities\normalizers;

use core\connectors\QueryBase;
/**
 * Description of DBNormalizer
 *
 * @author matthewe
 */
class DBNormalizer implements INormalizer {

  public function arrayToModelsCollection(array $resultsCollection, $modelNamespace = null, $formatter = null) {
    $returnCollection  = [];
    foreach ($resultsCollection as $resultSet) {
      $returnCollection[] = $this->arrayToModel($resultSet, $modelNamespace, $formatter);
    }
    return $returnCollection;
  }
  
  public function arrayToModel(array $resultsSet, $modelNamespace = null, $formatter = null) {
    $modelReflector = new \ReflectionClass($modelNamespace);
    $tableAliases = $formatter;
    $resultsCollection = [];
    foreach($resultsSet as $columnAlias => $value) {
      $namespace = $tableAliases[$columnAlias]['namespace'];
      $property  = $tableAliases[$columnAlias]['property'];
      if ($namespace == $modelReflector->getName()) {
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
