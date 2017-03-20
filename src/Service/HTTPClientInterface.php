<?php

namespace BackupMigrate\Core\Service;

use BackupMigrate\Core\File\ReadableStreamBackupFile;

/**
 * Interface HttpClientInterface
 * @package BackupMigrate\Core\Service
 */
interface HttpClientInterface {

  /**
   * Get the body of the given resource.
   *
   * @param $url
   * @return mixed
   */
  public function get($url);

  /**
   * Post the given data (as a string or an array) to the given URL
   *
   * @param $url
   * @param $data
   * @return mixed
   */
  public function post($url, $data);

  /**
   * Post a file along with other data (as an array)
   *
   * @param $url
   * @param \BackupMigrate\Core\File\ReadableStreamBackupFile $file
   * @param $data
   * @return mixed
   */
  public function postFile($url, ReadableStreamBackupFile $file, $data);
}
