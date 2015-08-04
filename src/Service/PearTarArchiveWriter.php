<?php
/**
 * @file
 * Contains BackupMigrate\Core\Environment\PearTarArchiveWriter
 */


namespace BackupMigrate\Core\Service;


use BackupMigrate\Core\Exception\BackupMigrateException;
use BackupMigrate\Core\File\BackupFileReadableInterface;
use Archive_Tar;

/**
 * Class PearTarArchiveWriter
 * @package BackupMigrate\Core\Environment
 */
class PearTarArchiveWriter implements ArchiveWriterInterface {
  use PearTarArchiveTrait;

  /**
   * @param \BackupMigrate\Core\File\BackupFileReadableInterface $out
   */
  public function setOutput(BackupFileReadableInterface $out) {
    $this->setArchiveFile($out);
  }

  /**
   * @param string $real_path
   *  The real path to the file. Can be a stream URI.
   * @param string $base_dir
   *  The base directory of the path to be removed when the file is added.
   * @throws \BackupMigrate\Core\Exception\BackupMigrateException
   */
  public function addFile($real_path, $base_dir = '') {
    $tar = $this->getArchiveTar();

    // Add the file to the tarball.
    $tar->addModify(array($real_path), '', $base_dir);
  }
}