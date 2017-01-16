<?php

namespace utilities\normalizers;

/**
 *
 * @author matthewe
 */
interface INormalizer {
  public function arrayToModelsCollection();
  public function arrayToModel();
  public function modelToArray();
  public function modelToJson();
}
