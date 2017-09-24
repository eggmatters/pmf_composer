<?php

namespace utilities\normalizers;

/**
 *
 * @author matthewe
 */
interface INormalizer {
  public function arrayToModelsCollection(array $resultsCollection, $modelNamespace = null, $formatter = null);
  public function arrayToModel(array $resultsCollection, $modelNamespace = null, $formatter = null);
  public function modelToArray($modelInstance, $formatter = null);
  public function modelToJson($modelInstance, $formatter = null);
}
