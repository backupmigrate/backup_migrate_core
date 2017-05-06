<?php
/**
 * @file
 * Contains BackupMigrate\Core\Destination\ServerDirectoryDestination
 */


namespace BackupMigrate\Core\Destination;


use BackupMigrate\Core\Config\ConfigurableInterface;
use BackupMigrate\Core\Exception\DestinationNotWritableException;
use BackupMigrate\Core\Plugin\FileProcessorInterface;
use BackupMigrate\Core\File\BackupFile;
use BackupMigrate\Core\File\BackupFileInterface;
use BackupMigrate\Core\File\BackupFileReadableInterface;
use BackupMigrate\Core\File\ReadableStreamBackupFile;

/**
 * Class ServerDirectoryDestination
 * @package BackupMigrate\Core\Destination
 */
class DirectoryDestination extends DestinationBase implements ListableDestinationInterface, ReadableDestinationInterface, ConfigurableInterface, FileProcessorInterface {
  use SidecarMetadataDestinationTrait;

  /**
   * {@inheritdoc}
   */
  function saveFile(BackupFileReadableInterface $file) {
    $this->_saveFile($file);
    $this->_saveFileMetadata($file);
  }

  /**
   * {@inheritdoc}
   */
  public function checkWritable() {
    $this->checkDirectory();
  }

  /**
   * Get a definition for user-configurable settings.
   *
   * @param array $params
   * @return array
   */
  public function configSchema($params = array()) {
    $schema = array();

    // Init settings.
    if ($params['operation'] == 'initialize') {
      $schema['fields']['directory'] = [
        'type' => 'text',
        'title' => $this->t('Directory Path'),
      ];
    }

    return $schema;
  }


  /**
   * Do the actual file save. This function is called to save the data file AND
   * the metadata sidecar file.
   * @param \BackupMigrate\Core\File\BackupFileReadableInterface $file
   * @throws \BackupMigrate\Core\Exception\BackupMigrateException
   */
  function _saveFile(BackupFileReadableInterface $file) {
    // Check if the directory exists.
    $this->checkDirectory();

    copy($file->realpath(), $this->_idToPath($file->getFullName()));
    // @TODO: use copy/unlink if the temp file and the destination do not share a stream wrapper.
  }

  /**
   * Check that the directory can be used for backup.
   *
   * @throws \BackupMigrate\Core\Exception\BackupMigrateException
   */
  protected function checkDirectory() {
    $dir = $this->confGet('directory');

    // Check if the directory exists.
    if (!file_exists($dir)) {
      throw new DestinationNotWritableException(
        "The backup file could not be saved to '%dir' because it does not exist.",
        ['%dir' => $dir]
      );
    }

    // Check if the directory is writable
    if (!is_writable($this->confGet('directory'))) {
      throw new DestinationNotWritableException(
        "The backup file could not be saved to '%dir' because Backup and Migrate does not have write access to that directory.",
        ['%dir' => $dir]
      );
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getFile($id) {
    if ($this->fileExists($id)) {
      $out = new BackupFile();
      $out->setMeta('id', $id);
      $out->setFullName($id);
      return $out;
    }
    return NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function loadFileForReading(BackupFileInterface $file) {
    // If this file is already readable, simply return it.
    if ($file instanceof BackupFileReadableInterface) {
      return $file;
    }

    $id = $file->getMeta('id');
    if ($this->fileExists($id)) {
      return new ReadableStreamBackupFile($this->_idToPath($id));
    }
    return NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function listFiles() {
    $dir = $this->confGet('directory');
    $out = array();

    // Get the entire list of filenames
    $files = $this->_getAllFileNames();

    foreach ($files as $file) {
      $filepath = $dir . '/' . $file;
      $out[$file] = new ReadableStreamBackupFile($filepath);
    }

    return $out;
  }

  /**
   * {@inheritdoc}
   */
  public function queryFiles(
    $filters = [],
    $sort = 'datestamp',
    $sort_direction = SORT_DESC,
    $count = 100,
    $start = 0
  ) {

    // Get the full list of files.
    $out = $this->listFiles($count + $start);
    foreach ($out as $key => $file) {
      $out[$key] = $this->loadFileMetadata($file);
    }

    // Filter the output.
    if ($filters) {
      $out = array_filter($out, function($file) use ($filters) {
        foreach ($filters as $key => $value) {
          if ($file->getMeta($key) !== $value) {
            return false;
          }
        }
        return true;
      });
    }

    // Sort the files.
    if ($sort && $sort_direction) {
      uasort($out, function ($a, $b) use ($sort, $sort_direction) {
        if ($sort_direction == SORT_DESC) {
          return $b->getMeta($sort) < $b->getMeta($sort);
        }
        else {
          return $b->getMeta($sort) > $b->getMeta($sort);
        }
      });
    }

    // Slice the return array.
    if ($count || $start) {
      $out = array_slice($out, $start, $count);
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
  public function _deleteFile($id) {
    if ($file = $this->getFile($id)) {
      if ($file = $this->loadFileForReading($file)) {
        return unlink($file->realpath());
      }
    }
    return false;
  }

  /**
   * Return a file path for the given file id.
   * @param $id
   * @return string
   */
  protected function _idToPath($id) {
    return rtrim($this->confGet('directory'), '/') . '/' . $id;
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
    if ($handle = opendir($dir)) {
      while (FALSE !== ($file = readdir($handle))) {
        $filepath = $dir . '/' . $file;
        // Don't show hidden, unreadable or metadata files
        if (substr($file, 0, 1) !== '.' && is_readable($filepath) && substr($file, strlen($file) - 5) !== '.info') {
          $files[] = $file;
        }
      }
    }

    return $files;
  }

}
