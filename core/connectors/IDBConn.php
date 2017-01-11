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
interface IDBConn {
  public static function getCollectionFromResultsSet(array $resultsSet, QueryBase $queryBuilder);
  public static function getModelFromResultsSet(array $resultsSet, QueryBase $queryBuilder);
}
