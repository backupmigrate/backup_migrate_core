<?php
/**
 * @file
 * Contains BackupMigrate\Core\Environment\PearTarArchiveBase
 */


namespace BackupMigrate\Core\Service;


use Archive_Tar;
use BackupMigrate\Core\Exception\BackupMigrateException;

/**
 * Class PearTarArchiveBase
 * @package BackupMigrate\Core\Environment
 */
trait PearTarArchiveTrait {

  /**
   * @var Archive_Tar;
   */
  protected $archive_tar;
  
  /**
   * Get the file extension for this archiver. For a tarball writer this would
   * be 'tar'. For a Zip file writer this would be 'zip'.
   *
   * @return string
   */
  public function getFileExt() {
    return 'tar';
  }

  /**
   * @return \Archive_Tar Archive_Tar
   * @throws \BackupMigrate\Core\Exception\BackupMigrateException
   */
  protected function getArchiveTar() {
    if (!$this->archive_tar) {
      throw new BackupMigrateException('You must set an output file before using the tar archive writer.');
    }

    return $this->archive_tar;
  }

  /**
   * @param mixed $archive_file
   * @throws \BackupMigrate\Core\Exception\BackupMigrateException
   */
  protected function setArchiveFile($archive_file) {
    if (!class_exists('Archive_Tar')) {
      throw new BackupMigrateException('Archiving file directories requires the PEAR Archive_Tar class.');
    }

    $this->archive_tar = new Archive_Tar($archive_file->realpath());
  }

  /**
   * This will be called when all files have been added. It gives the implementation
   * a chance to clean up and commit the changes if needed.
   *
   * @return mixed
   */
  public function closeArchive() {
    // Remove the archive tar object in case this writer is reused.
    unset($this->archive_tar);
  }

}