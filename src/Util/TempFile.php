<?php

/**
 * @file
 * Contains \BackupMigrate\Core\Util\TempFile.
 */

// Must be injected:
// Temp directory

namespace BackupMigrate\Core\Util;

use BackupMigrate\Core\Util\BackupFile;
use BackupMigrate\Core\Services\TempFileAdapterInterface;

/**
 * Class TempFile
 * @package BackupMigrate\Core\Util
 *
 * A temporary file object that can be written to and read from.
 */
class TempFile extends BackupFile implements BackupFileWritableInterface  {

  /**
   * Constructor. Create a new file object from 
   */
  function __construct($filepath) {
    parent::__construct($filepath);
  }

  /**
   * Open a file for reading or writing.
   *
   * @param bool $binary Is the file binary
   * @throws \Exception
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
  }

  /**
   * Write a line to the file.
   * 
   * @param string $data A string to write to the file.
   * @throws \Exception
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

}
