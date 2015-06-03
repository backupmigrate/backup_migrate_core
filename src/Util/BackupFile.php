<?php

/**
 * @file
 * Contains \BackupMigrate\Core\Util\BackupFile.
 */

namespace BackupMigrate\Core\Util;

use BackupMigrate\Core\Util\BackupFileInterface;

class BackupFile implements BackupFileReadableInterface {
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
  protected $handle;
  
  /**
   * The file's metadata
   * 
   * @var array A key/value associative array of metadata.
   */
  protected $metadata;

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
   * Destructor.
   */
  function __destruct() {
    // Close the handle if we've opened it.
    $this->close();
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
  function openForRead($binary = FALSE) {
    if (!$this->isOpen()) {
      $path = $this->realpath();

      if (!is_readable($path)) {
        // @TODO: Throw better exception
        throw new \Exception('Cannot read file.');
      }

      // Open the file.
      $mode = "r" . ($binary ? "b" : "");
      $this->handle = fopen($path, $mode);
      if (!$this->handle) {
        throw new \Exception('Cannot open file.');
      }
    }
    return $this->handle;
  }

  /**
   * Close a file when we're done reading/writing.
   */
  function close() {
    if ($this->isOpen()) {
      fclose($this->handle);
      $this->handle = NULL;
    }
  }

  /**
   * Is this file open for reading/writing.
   * 
   * return bool True if the file is open, false if not.
   */
  function isOpen() {
    return !empty($this->handle);
  }
  
  /**
   * Read a line from the file.
   * 
   * @param int $size The number of bites to read or 0 to read the whole file
   * @return string The data read from the file or NULL if the file can't be read or is at the end of the file.
   */
  function read($size = 0, $binary = FALSE) {
    if (!$this->isOpen()) {
      $this->openForRead($binary);
    }
    if ($this->handle && !feof($this->handle)) {
      return $size ? fread($this->handle, $size) : fgets($this->handle);
    }
    return NULL;
  }

  /**
   * Rewind the file handle to the start of the file.
   */
  function rewind() {
    if ($this->isOpen()) {
      rewind($this->handle);
    }
  }

  /**
   * Get a metadata value
   *
   * @param string $key The key for the metadata item.
   * @return mixed The value of the metadata for this file.
   */
  public function getMeta($key) {
    return isset($this->medatata[$key]) ? $this->medatata[$key] : NULL;
  }

  /**
   * Set a metadata value
   *
   * @param string $key The key for the metadata item.
   * @param mixed $value The value for the metadata item.
   */
  public function setMeta($key, $value) {
    $this->medatata[$key] = $value;
  }

  /**
   * Set a metadata value
   *
   * @param array $values An array of key-value pairs for the file metadata.
   */
  public function setMetaMultiple($values) {
    foreach ($values as $key => $value) {
      $this->setMeta($key, $value);
    }
  }
}
