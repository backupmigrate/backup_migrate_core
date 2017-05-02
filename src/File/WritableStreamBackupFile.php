<?php

/**
 * @file
 * Contains \BackupMigrate\Core\File\TempFile.
 */

// Must be injected:
// Temp directory

namespace BackupMigrate\Core\File;

use BackupMigrate\Core\Exception\BackupMigrateException;

/**
 * Class TempFile
 * @package BackupMigrate\Core\File
 *
 * A file object which represents an existing PHP stream that can be written to and read from.
 */
class WritableStreamBackupFile extends ReadableStreamBackupFile implements BackupFileReadableInterface, BackupFileWritableInterface  {

  /**
   * @var bool Dirty bit. Has the file been written to since it was opened?
   */
  protected $dirty = FALSE;

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
      if ((file_exists($path) && !is_writable($path)) || (!file_exists($path) && !is_writable(dirname($path)))) {
        // @TODO: Throw better exception
        throw new BackupMigrateException('Cannot write to file: %path', ['%path' => $path]);
      }

      // Open the file.
      $mode = "w" . ($binary ? "b" : "");
      $this->handle = fopen($path, $mode);
      if (!$this->handle) {
        throw new BackupMigrateException('Cannot open file: %path', ['%path' => $path]);
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
      if (fwrite($this->handle, $data) === FALSE) {
        throw new \Exception('Cannot write to file: ' . $this->realpath());
      }
      else {
        $this->dirty = TRUE;
      }
    }
    else {
      throw new \Exception('File not open for writing.');
    }
  }


  /**
   * Update the file time and size when the file is closed.
   */
  function close() {
    parent::close();

    // If the file has been modified, update the stats from disk.
    if ($this->dirty) {
      $this->_loadFileStats();
      $this->dirty = FALSE;
    }
  }

  /**
   * A shorthand function to open the file, write the given contents and close
   * the file. Used for small amounts of data that can fit in memory.
   *
   * @param $data
   */
  public function writeAll($data) {
    $this->openForWrite();
    $this->write($data);
    $this->close();
  }
}
