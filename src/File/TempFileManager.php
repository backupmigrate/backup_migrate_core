<?php
/**
 * @file
 * Contains \BackupMigrate\Core\Services\TempFileManagerInterface.
 */

namespace BackupMigrate\Core\File;

use BackupMigrate\Core\Plugin\PluginCallerInterface;
use BackupMigrate\Core\Plugin\PluginCallerTrait;

/**
 * Class TempFileManager
 * @package BackupMigrate\Core\Services
 */
class TempFileManager implements TempFileManagerInterface {

  /**
   * @var \BackupMigrate\Core\File\TempFileAdapterInterface
   */
  protected $adapter;

  /**
   * Build the manager with the given adapter. This manager needs the adapter
   * to create the actual temp files.
   *
   * @param \BackupMigrate\Core\File\TempFileAdapterInterface $adapter
   */
  public function __construct(TempFileAdapterInterface $adapter) {
    $this->adapter = $adapter;
  }

  /**
   * Create a brand new temp file with the given extension (if specified). The
   * new file should be writable.
   *
   * @param string $ext The file extension for this file (optional)
   * @return BackupFileWritableInterface
   */
  public function create($ext = '') {
    $file = new WritableStreamBackupFile($this->adapter->createTempFile($ext));
    $file->setExtList(explode('.', $ext));
    return $file;
  }

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
  public function pushExt(BackupFileInterface $file, $ext) {
    // Push the new extension on to the new file
    $parts = $file->getExtList();
    array_push($parts, $ext);
    $new_ext = implode($parts, '.');

    // Copy the file metadata to a new TempFile
    $out = new WritableStreamBackupFile($this->adapter->createTempFile($new_ext));

    // Copy the file metadata to a new TempFile
    $out->setMetaMultiple($file->getMetaAll());
    $out->setName($file->getName());
    $out->setExtList($parts);

    return $out;
  }

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
  public function popExt(BackupFileInterface $file) {
    // Pop the last extension from the last of the file.
    $parts = $file->getExtList();
    array_pop($parts);
    $new_ext = implode($parts, '.');

    // Create a new temp file with the new extension
    $out = new WritableStreamBackupFile($this->adapter->createTempFile($new_ext));

    // Copy the file metadata to a new TempFile
    $out->setMetaMultiple($file->getMetaAll());
    $out->setName($file->getName());
    $out->setExtList($parts);

    return $out;
  }


}