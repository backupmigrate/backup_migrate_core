<?php

/**
 * @file
 * Contains \BackupMigrate\Core\Util\TempFile.
 */

// Must be injected:
// Temp directory

namespace BackupMigrate\Core\Util;

use BackupMigrate\Core\Util\BackupFile;

class TempFile extends BackupFile {

  /**
   * Constructor. Create a new file object from 
   */
  function __construct() {
    // @TODO: Allow the temp directory to be specified by injection.
    $path = tempnam('/tmp', 'bam');
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
   * Write a line to the file.
   * 
   * @param string $data A string to write to the file.
   */
  function write($data) {
    if (!$this->isOpen()) {
      $this->open(TRUE);
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

  // /**
  //  * Delete the file.
  //  */
  // function delete() {
  //   if ($path = $this->realpath()) {
  //     if (file_exists($path) && (is_writable($path) || is_link($path))) {
  //       unlink($path);
  //     }
  //     else {
  //       // @TODO: Throw an exception because we can't delete this file.
  //     }
  //   }
  // }

}
