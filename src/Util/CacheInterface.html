<?php

/**
 * @file
 * Definition of BackupMigrate\Core\Util\CacheInterface.
 */

namespace BackupMigrate\Core\Util;

/**
 * Defines an interface for cache implementations. 
 * 
 * Caching is not required by Backup and Migrate but it can be used to improve 
 * performance especially when using remote destinations and other high latency 
 * resources.
 *
 */
interface CacheInterface {

  const CACHE_PERMANENT = -1;

  /**
   * Returns data from the persistent cache.
   *
   * @param string $cid
   *   The cache ID of the data to retrieve.
   * @param bool $allow_invalid
   *   (optional) If TRUE, a cache item may be returned even if it is expired or
   *   has been invalidated. Such items may sometimes be preferred, if the
   *   alternative is recalculating the value stored in the cache, especially
   *   if another concurrent request is already recalculating the same value.
   *   The "valid" property of the returned object indicates whether the item is
   *   valid or not. Defaults to FALSE.
   *
   * @return object|false
   *   The cache item or FALSE on failure.
   *
   */
  public function get($cid, $allow_invalid = FALSE);

  /**
   * Stores data in the persistent cache.
   *
   * Core cache implementations set the created time on cache item with
   * microtime(TRUE) rather than REQUEST_TIME_FLOAT, because the created time
   * of cache items should match when they are created, not when the request
   * started. Apart from being more accurate, this increases the chance an
   * item will legitimately be considered valid.
   *
   * @param string $cid
   *   The cache ID of the data to store.
   * @param mixed $data
   *   The data to store in the cache.
   *   Some storage engines only allow objects up to a maximum of 1MB in size to
   *   be stored by default. When caching large arrays or similar, take care to
   *   ensure $data does not exceed this size.
   * @param int $expire
   *   One of the following values:
   *   - CacheInterface::CACHE_PERMANENT: Indicates that the item should
   *     not be removed unless it is deleted explicitly.
   *   - A Unix timestamp: Indicates that the item will be considered invalid
   *     after this time, i.e. it will not be returned by get() unless
   *     $allow_invalid has been set to TRUE. When the item has expired, it may
   *     be permanently deleted by the garbage collector at any time.
   */
  public function set($cid, $data, $expire = CacheInterface::CACHE_PERMANENT);


  /**
   * Deletes an item from the cache.
   *
   * If the cache item is being deleted because it is no longer "fresh", you may
   * consider using invalidate() instead. This allows callers to retrieve the
   * invalid item by calling get() with $allow_invalid set to TRUE. In some cases
   * an invalid item may be acceptable rather than having to rebuild the cache.
   *
   * @param string $cid
   *   The cache ID to delete.
   */
  public function delete($cid);
}
