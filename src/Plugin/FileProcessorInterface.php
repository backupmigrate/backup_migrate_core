<?php
/**
 * @file
 */

namespace BackupMigrate\Core\Plugin;


use BackupMigrate\Core\File\TempFileManagerInterface;

/**
 * Interface FileProcessorPluginInterface
 * @package BackupMigrate\Core\Plugin
 *
 * An interface for plugins which process files and therefore must have access
 * to a temp file factory.
 */
interface FileProcessorInterface {

  /**
   * Inject the temp file manager.
   *
   * @param \BackupMigrate\Core\File\TempFileManagerInterface $tempfilemanager
   * @return mixed
   */
  public function setTempFileManager(TempFileManagerInterface $tempfilemanager);

  /**
   * Get the temp file manager.

   * @return \BackupMigrate\Core\File\TempFileManagerInterface
   */
  public function getTempFileManager();


}