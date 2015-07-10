<?php
/**
 * @file
 * Contains \BackupMigrate\Core\Services\TempFileManagerInterface.
 */

namespace BackupMigrate\Core\File;

use BackupMigrate\Core\File\BackupFileInterface;
use BackupMigrate\Core\File\BackupFileWritableInterface;

/**
 * Interface TempFileManagerInterface
 * @package BackupMigrate\Core\Services
 *
 * A TempFileManager is in charge of creating new temp files for writing
 * and tracking all created files so they can be deleted. It is also
 * in charge of copying metadata from one temp file to another as the file
 * gets sent through the chain of plugins.
 */
interface TempFileManagerInterface {

  /**
   * Create a brand new temp file with the given extension (if specified). The
   * new file should be writable.
   *
   * @param string $ext The file extension for this file (optional)
   * @return BackupFileWritableInterface
   */
  public function create($ext = '');

  /**
   * Return a new file based on the passed in file with the given file extension.
   * This should maintain the metadata of the file passed in with the new file
   * extension added after the old one.
   * For example: xxx.mysql would become xxx.mysql.gz
   *
   *
   * @param \BackupMigrate\Core\File\BackupFileInterface $file
   *        The file to add the extension to.
   * @param $ext
   *        The new file extension.
   * @return \BackupMigrate\Core\File\BackupFileWritableInterface
   *        A new writable backup file with the new extension and all of the metadata
   *        from the previous file.
   */
  public function pushExt(BackupFileInterface $file, $ext);

  /**
   * Return a new file based on the one passed in but with the last part of the
   * file extension removed.
   * For example: xxx.mysql.gz would become xxx.mysql
   *
   *
   * @param \BackupMigrate\Core\File\BackupFileInterface $file
   * @return \BackupMigrate\Core\File\BackupFileWritableInterface
   *        A new writable backup file with the last extension removed and
   *        all of the metadata from the previous file.
   */
  public function popExt(BackupFileInterface $file);

}