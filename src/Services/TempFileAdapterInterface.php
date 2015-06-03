<?php

/**
 * @file
 * Contains \BackupMigrate\Core\Services\TempFileManagerInterface.
 */

namespace BackupMigrate\Core\Services;

/**
 * Provides a service to provision temp files in the correct place for the environment.
 */
interface TempFileAdapterInterface {
  /**
   * Get a temporary file that can be written to
   * 
   * @return string The path to the file.
   */
  public function createTempFile();

  /**
   * Delete a temporary file.
   * 
   * @param string $filename The path to the file.
   */
  public function deleteTempFile($filename);

  /**
   * Delete all temp files which have been created.
   */
  public function deleteAllTempFiles();
}
