<?php

namespace utilities\cache;

/**
 *
 * @author matthewe
 */
interface ICache {
  public static function isCacheEnabled();
  public static function enableCache();
  public function setCachedObject($object, $key);
  public function setCachedArray($array, $key);
  public function setCachedValue($value, $key);
  public function getCachedObject($key);
  public function getCachedArray($key);
  public function getCachedValue($key);
}
