<?php
/**
 * @file
 */

namespace BackupMigrate\Core\Service;


use BackupMigrate\Core\File\BackupFileReadableInterface;

/**
 * Interface ArchiveWriterInterface
 *
 * @package BackupMigrate\Core\Environment
 */
interface ArchiveReaderInterface {

  /**
   * Get the file extension for this archiver. For a tarball writer this would
   * be 'tar'. For a Zip file writer this would be 'zip'.
   *
   * @return string
   */
  public function getFileExt();

  /**
   * @param \BackupMigrate\Core\File\BackupFileReadableInterface $out
   */
  public function setArchive(BackupFileReadableInterface $out);

  /**
   * Extract all files to the given directory.
   *
   * @param $directory
   * @return mixed
   */
  public function extractTo($directory);

  // public function listFiles()
  // public function extractFile($from, $to);

  /**
   * This will be called when all files have been added. It gives the implementation
   * a chance to clean up and commit the changes if needed.
   *
   * @return mixed
   */
  public function closeArchive();
}