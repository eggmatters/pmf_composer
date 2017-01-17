<?php

namespace utilities\cache;

/**
 *
 * @author matthewe
 */
interface ICache {
  public function isCacheEnabled();
  public function setCachedObject($object, $key);
  public function setCachedArray($array, $key);
  public function setCachedValue($value, $key);
  public function getCachedObject($key);
  public function getCachedArray($key);
  public function getCachedValue($key);
}
