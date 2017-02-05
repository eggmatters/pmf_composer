<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace core\connectors;
use utilities\normalizers\INormalizer;
/**
 *
 * @author matthewe
 */
interface IConnector {
  public function getAll();
  public function get($id = null);
  public function create($params);
  public function update($id, $params);
  public function delete($params = null);
}
