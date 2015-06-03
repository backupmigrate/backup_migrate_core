<?php

/**
 * @file
 * Contains \BackupMigrate\Core\Util\TempFile.
 */

// Must be injected:
// Temp directory

namespace BackupMigrate\Core\Util;

use BackupMigrate\Core\Util\BackupFile;
use BackupMigrate\Core\Services\TempFileManagerInterface;

class TempFile extends BackupFile implements BackupFileWritableInterface  {

  /**
   * Injected filemanager to provision and delete actual files
   * 
   * @var TempFileManagerInterface
   */
  protected $filemanager;

  /**
   * Constructor. Create a new file object from 
   */
  function __construct($filemanager) {
    $this->filemanager = $filemanager;

    // Request a new temporary file from the temp file manager.
    $path = $this->filemanager->createTempFile();
    parent::__construct($path);
  }

  /**
   * Destructor. Delete the temporary file.
   */
  function __destruct() {
    $this->delete();
  }

  // /**
  //  * Make the file permanent by moving it to the given file path.
  //  * 
  //  * @param string $path The destination path (without filename) as a file path or stream URI.
  //  * @param string $filename The destination filename without an extension.
  //  */
  // function save($path, $filename) {
  //   $to = $path . $filename . $this->getExtension();
  //   if (rename($this->realpath(), $to)) {
  //     $out = new BackupFile($to);
  //     return $out;
  //   }
  //   // @TODO throw an exception. The file could not be moved
  //   return NULL;
  // }

  /**
   * Open a file for reading or writing.
   * 
   * @param bool $write If tre open for writing, otherwise open for reading only
   * @param bool $binary If true open as a binary file
   */
  function openForWrite($binary = FALSE) {
    if (!$this->isOpen()) {
      $path = $this->realpath();

      // Check if the file can be read/written.
      if ((file_exists($path) && !is_writable($path)) || !is_writable(dirname($path))) {
        // @TODO: Throw better exception
        throw new \Exception('Cannot write to file.');
      }

      // Open the file.
      $mode = "w" . ($binary ? "b" : "");
      $this->handle = fopen($path, $mode);
      if (!$this->handle) {
        throw new \Exception('Cannot open file.');
      }
    }
    return $this->handle;
  }

  /**
   * Write a line to the file.
   * 
   * @param string $data A string to write to the file.
   */
  function write($data) {
    if (!$this->isOpen()) {
      $this->openForWrite();
    }

    if ($this->handle) {
      if (!fwrite($this->handle, $data)) {
        throw new \Exception('Cannot write to file.');
      }
    }
    else {
      throw new \Exception('File not open for writing.');
    }
  }

  /**
   * Delete the file.
   */
  function delete() {
    $this->filemanager->deleteTempFile($this->path);
  }

}
