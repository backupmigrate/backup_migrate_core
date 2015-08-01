<?php
/**
 * @file
 * Contains BackupMigrate\Core\Environment\PearTarArchiveReader
 */


namespace BackupMigrate\Core\Environment;


use BackupMigrate\Core\Exception\BackupMigrateException;
use BackupMigrate\Core\File\BackupFileReadableInterface;

/**
 * Class PearTarArchiveReader
 * @package BackupMigrate\Core\Environment
 */
class PearTarArchiveReader implements ArchiveReaderInterface {
  use PearTarArchiveTrait;

  /**
   * @param \BackupMigrate\Core\File\BackupFileReadableInterface $in
   *  The file object to read from.
   * @return null
   * @throws \BackupMigrate\Core\Exception\BackupMigrateException
   */
  public function setInput(BackupFileReadableInterface $in) {
    $this->setArchiveFile($in);
  }

  /**
   * Extract all files to the given directory.
   *
   * @param string $directory
   *  The directory to extract the files to.
   * @return null
   * @throws \BackupMigrate\Core\Exception\BackupMigrateException
   */
  public function extractTo($directory) {
    $tar = $this->getArchiveTar();
    $tar->extract($directory);
  }

}