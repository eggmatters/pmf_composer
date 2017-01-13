<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace core\connectors;

/**
 *
 * @author matthewe
 */
interface IConnector {
  /**
   * All derived connector class will implement this method to apply fetched results to a 
   * collection that will be accepted by a model constructor.
   * @param array $resultsSet
   * @param mixed $normalizer
   */
  public function normalizeResultsSet(array $resultsSet, $normalizer);
  public function normalizeResultsCollection(array $resultsCollection, $normalizer);
}
