<?php

/**
 * @file
 * Contains \BackupMigrate\Core\Util\BackupFile.
 */

namespace BackupMigrate\Core\Util;


class BackupFile {
  /**
   * The file info (size, timestamp, etc.).
   *
   * @var array
   */
  protected $file_info;

  /**
   * The file path.
   *
   * @var string
   */
  protected $path;

  /**
   * The file name.
   *
   * @var string
   */
  protected $name;

  /**
   * A file handle if it is open.
   *
   * @var resource
   */
  private $handle;
  
  /**
   * Constructor.
   * 
   * @param string $filepath string The path to a file (which must already exist). Can be a stream URI.
   */
  function __construct($filepath) {
    $this->path = $filepath;
    //@TODO check that this file exists and is readable/writeable.
  }


  /**
   * Get the realpath of the file
   * 
   * @return string The path or stream URI to the file or NULL if the file does not exist.
   */
  function realpath() {
    if (file_exists($this->path)) {
      return $this->path;
    }
    return NULL;
  }

  /**
   * Open a file for reading or writing.
   * 
   * @param bool $write If tre open for writing, otherwise open for reading only
   * @param bool $binary If true open as a binary file
   */
  function open($write = FALSE, $binary = FALSE) {
    if (!$this->handle) {
      $path = $this->filepath();

      // Check if the file can be read/written.
      if ($write && ((file_exists($path) && !is_writable($path)) || !is_writable(dirname($path)))) {
        _backup_migrate_message('The file %path cannot be written to.', array('%path' => $path), 'error');
        return FALSE;
      }
      if (!$write && !is_readable($path)) {
        _backup_migrate_message('The file %path cannot be read.', array('%path' => $path), 'error');
        return FALSE;
      }

      // Open the file.
      $mode = ($write ? "w" : "r") . ($binary ? "b" : "");
      $this->handle = fopen($path, $mode);
      return $this->handle;
    }
    return NULL;
  }

  /**
   * Close a file when we're done reading/writing.
   */
  function close() {
    fclose($this->handle);
    $this->handle = NULL;
  }

  /**
   * Write a line to the file.
   * 
   * @param string $data A string to write to the file.
   */
  function write($data) {
    if (!$this->handle) {
      $this->handle = $this->open(TRUE);
    }
    if ($this->handle) {
      fwrite($this->handle, $data);
    }
  }

  /**
   * Read a line from the file.
   * 
   * @param int $size The number of bites to read or 0 to read the whole file
   * @return string The data read from the file or NULL if the file can't be read or is at the end of the file.
   */
  function read($size = 0) {
    if (!$this->handle) {
      $this->handle = $this->open();
    }
    if ($this->handle && !feof($this->handle)) {
      return $size ? fread($this->handle, $size) : fgets($this->handle);
    }
    return NULL;
  }

  /**
   * Delete the file.
   */
  function delete() {
    if ($path = $this->realpath()) {
      if (file_exists($path) && (is_writable($path) || is_link($path))) {
        unlink($path);
      }
      else {
        // @TODO: Throw an exception because we can't delete this file.
      }
    }
  }

}
