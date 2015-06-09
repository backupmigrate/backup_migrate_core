<?php

/**
 * @file
 * Contains \BackupMigrate\Core\Destination\DestinationBase.
 */

namespace BackupMigrate\Core\Destination;

use BackupMigrate\Core\Config\ConfigurableTrait;
use BackupMigrate\Core\Util\BackupFileInterface;
use BackupMigrate\Core\Util\BackupFileReadableInterface;

/**
 * Class DestinationBase
 * @package BackupMigrate\Core\Destination
 */
abstract class DestinationBase implements DestinationInterface
{
  use ConfigurableTrait;

  /**
   * {@inheritdoc}
   */
  public function saveFile(BackupFileReadableInterface $file) {
    $this->_saveFile($file);
    $this->_saveFileMetadata($file);
  }

  /**
   * {@inheritdoc}
   */
  public function loadFileMetadata(BackupFileInterface $file) {
    // If this file is already loaded, simply return it.
    // @TODO: fix this inappropriate use of file metadata
    if (!$file->getMeta('metadata_loaded')) {
      $metadata = $this->_loadFileMetadataArray($file);
      $file->setMetaMultiple($metadata);
      $file->setMeta('metadata_loaded', TRUE);
    }
    return $file;
  }

  /**
   * {@inheritdoc}
   */
  public function deleteFile($id) {
    return $this->_deleteFile($id);
  }

  /**
   * Is this a remote destination.
   *
   * @return bool True if remote, false if local.
   */
  public function isRemote() {
    return false;
  }

  /**
   * Do the actual delete for a file.
   *
   * @param string $id The id of the file to delete.
   */
  abstract protected function _deleteFile($id);

  /**
   * Do the actual file save. Should take care of the actual creation of a file
   * in the destination without regard for metadata.
   * @param \BackupMigrate\Core\Util\BackupFileReadableInterface $file
   */
  abstract protected function _saveFile(BackupFileReadableInterface $file);

  /**
   * Do the metadata save. This function is called to save the data file AND
   * the metadata sidecar file.
   * @param \BackupMigrate\Core\Util\BackupFileInterface $file
   */
  abstract protected function _saveFileMetadata(BackupFileInterface $file);

  /**
   * Load the actual metadata for the file
   *
   * @param \BackupMigrate\Core\Util\BackupFileInterface $file
   */
  abstract protected function _loadFileMetadataArray(BackupFileInterface $file);

}
