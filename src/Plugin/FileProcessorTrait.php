<?php
/**
 * @file
 */

namespace BackupMigrate\Core\Plugin;


use BackupMigrate\Core\Services\TempFileManagerInterface;

/**
 * Class FileProcessorPluginTrait
 * @package BackupMigrate\Core\Plugin
 *
 * Implement the injection functionality of a file processor.
 */
trait FileProcessorTrait
{
  /**
   * @var TempFileManagerInterface
   */
  protected $tempfilemanager;

  /**
   * Inject the temp file manager.
   *
   * @param \BackupMigrate\Core\Services\TempFileManagerInterface $tempfilemanager
   * @return mixed
   */
  public function setTempFileManager(TempFileManagerInterface $tempfilemanager) {
    $this->tempfilemanager = $tempfilemanager;
  }

  /**
   * Get the temp file manager.
   * @return \BackupMigrate\Core\Services\TempFileManagerInterface
   */
  public function getTempFileManager() {
    return $this->tempfilemanager;
  }



}