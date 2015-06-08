<?php
/**
 * @file
 * Contains BackupMigrate\Core\Destination\ServerDirectoryDestination
 */


namespace BackupMigrate\Core\Destination;


use BackupMigrate\Core\Config\ConfigurableInterface;
use BackupMigrate\Core\Config\ConfigurableTrait;
use BackupMigrate\Core\Util\BackupFileReadableInterface;
use BackupMigrate\Core\Util\ReadableStreamBackupFile;

/**
 * Class ServerDirectoryDestination
 * @package BackupMigrate\Core\Destination
 */
class ServerDirectoryDestination implements DestinationInterface, ConfigurableInterface {
  use ConfigurableTrait;

  /**
   * {@inheritdoc}
   */
  function saveFile(BackupFileReadableInterface $file) {
    rename($file->realpath(), $this->confGet('directory') . $file->getMeta('filename'));
    // @TODO: use copy/unlink if the temp file and the destination do not share a stream wrapper.
  }

  /**
   * {@inheritdoc}
   */
  public function loadFile($id) {
    if ($this->fileExists($id)) {
      return new ReadableStreamBackupFile($this->_idToPath($id));
    }
    return NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function listFiles($count = 100, $start = 0) {
    $dir = $this->confGet('directory');
    $out = array();

    // Get the entire list of filenames
    $files = $this->_getAllFileNames();

    // Limit to only the items specified.
    for ($i = $start; $i < min($start + $count, count($files)); $i++) {
      $file = $files[$i];
      $filepath = $dir . '/' . $file;
      $out[$file] = new ReadableStreamBackupFile($filepath);
      // @TODO: Load metadata from .info file if it exists.
    }

    return $out;
  }


  /**
   * @return int The number of files in the destination.
   */
  public function countFiles() {
    $files = $this->_getAllFileNames();
    return count($files);
  }


  /**
   * {@inheritdoc}
   */
  public function fileExists($id) {
    return file_exists($this->_idToPath($id));
  }

  /**
   * {@inheritdoc}
   */
  public function deleteFile($id) {
    if ($file = $this->loadFile($id)) {
      return unlink($file->realpath());
    }
  }

  /**
   * Return a file path for the given file id.
   * @param $id
   * @return string
   */
  protected function _idToPath($id) {
    return $this->confGet('directory') . $id;
  }

  /**
   * Get the entire file list from this destination.
   *
   * @return array
   */
  protected function _getAllFileNames() {
    $files = array();

    // Read the list of files from the directory.
    $dir = $this->confGet('directory');
    if ($handle = @opendir($dir)) {
      while (FALSE !== ($file = readdir($handle))) {
        $filepath = $dir . '/' . $file;
        // Don't show hidden or unreadable files
        // @TODO: Filter out unsupported and metadata files.
        if (substr($file, 0, 1) !== '.' && is_readable($filepath)) {
          $files[] = $file;
        }
      }
    }

    return $files;
  }
}