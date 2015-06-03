<?php
/**
 * @file
 * Contains \BackupMigrate\Core\Services\TempFileManagerInterface.
 */

namespace BackupMigrate\Core\Services;

use BackupMigrate\Core\Services\TempFileAdapterInterface;
use BackupMigrate\Core\Util\BackupFileInterface;
use BackupMigrate\Core\Util\BackupFileWritableInterface;
use BackupMigrate\Core\Util\TempFile;

/**
 * Class TempFileManager
 * @package BackupMigrate\Core\Services
 */
class TempFileManager implements TempFileManagerInterface {

  /**
   * @var \BackupMigrate\Core\Services\TempFileAdapterInterface
   */
  protected $adapter;

  /**
   * Build the manager with the given adapter. This manager needs the adapter
   * to create the actual temp files.
   *
   * @param \BackupMigrate\Core\Services\TempFileAdapterInterface $adapter
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
    $file = new TempFile($this->adapter->createTempFile());
    $file->setMeta('ext', $ext);
    return $file;
  }

  /**
   * Return a new file based on the passed in file with the given file extension.
   * This should maintain the metadata of the file passed in with the new file
   * extension added after the old one.
   * For example: xxx.mysql would become xxx.mysql.gz
   *
   *
   * @param \BackupMigrate\Core\Util\BackupFileInterface $file
   *        The file to add the extension to.
   * @param $ext
   *        The new file extension.
   * @return \BackupMigrate\Core\Util\BackupFileWritableInterface
   *        A new writable backup file with the new extension and all of the metadata
   *        from the previous file.
   */
  public function pushExt(BackupFileInterface $file, $ext) {
    // Copy the file metadata to a new TempFile
    $out = new TempFile($this->adapter->createTempFile());
    $file->setMetaMultiple($file->getMetaAll());

    // Push the new extension on to the new file
    $previous_ext = $file->getMeta('ext');
    $parts = explode('.', $previous_ext);
    array_push($parts, $ext);

    $out->setMeta('ext', implode('.', array_filter($parts)));

    return $out;
  }

  /**
   * Return a new file based on the one passed in but with the last part of the
   * file extension removed.
   * For example: xxx.mysql.gz would become xxx.mysql
   *
   *
   * @param \BackupMigrate\Core\Util\BackupFileInterface $file
   * @return \BackupMigrate\Core\Util\BackupFileWritableInterface
   *        A new writable backup file with the last extension removed and
   *        all of the metadata from the previous file.
   */
  public function popExt(BackupFileInterface $file) {
    // Copy the file metadata to a new TempFile
    $out = new TempFile($this->adapter->createTempFile());
    $file->setMetaMultiple($file->getMetaAll());

    // Pop the last extension from the last of the file.
    $ext = $file->getMeta('ext');
    $parts = explode('.', $ext);
    array_pop($parts);
    $out->setMeta('ext', implode('.', $parts));

    return $out;
  }
}