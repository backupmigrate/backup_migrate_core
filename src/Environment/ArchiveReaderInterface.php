<?php
/**
 * @file
 */

namespace BackupMigrate\Core\Environment;


use BackupMigrate\Core\File\BackupFileReadableInterface;

/**
 * Interface ArchiveReaderInterface
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
   * Set the input file to be read from.
   *
   * @param \BackupMigrate\Core\File\BackupFileReadableInterface $in
   * @return mixed
   */
  public function setInput(BackupFileReadableInterface $in);

  /**
   * Extract all files to the given directory.
   *
   * @param $directory
   * @return mixed
   */
  public function extractTo($directory);

  /**
   * This will be called when all files have been read. It gives the implementation
   * a chance to clean up if needed.
   *
   * @return mixed
   */
  public function closeArchive();

}