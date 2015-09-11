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
class PearTarArchiver implements ArchiverInterface {

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
   * @param \BackupMigrate\Core\File\BackupFileReadableInterface $out
   * @throws \BackupMigrate\Core\Exception\BackupMigrateException
   */
  public function setArchive(BackupFileReadableInterface $archive_file) {
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


  /**
   * @param string $real_path
   *  The real path to the file. Can be a stream URI.
   * @param string $new_path
   *  The new path this file should have in the archive.
   *  NB: Only the path part is used, the filename cannot be changed.
   *    eg: /tmp/somefile.txt -> some/tar/dir/somefile.txt
   * @throws \BackupMigrate\Core\Exception\BackupMigrateException
   */
  public function addFile($real_path, $new_path = '') {
    $tar = $this->getArchiveTar();

    $add = $remove = '';
    if ($new_path && $new_path !== $real_path) {
      $remove = dirname($real_path);
      $add = dirname($new_path);
    }

    // Add the file to the tarball.
    $tar->addModify(array($real_path), $add, $remove);
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