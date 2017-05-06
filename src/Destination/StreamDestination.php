<?php
/**
 * @file
 * Contains BackupMigrate\Core\Destination\StreamDestination
 */


namespace BackupMigrate\Core\Destination;


use BackupMigrate\Core\Config\ConfigurableInterface;
use BackupMigrate\Core\Exception\DestinationNotWritableException;
use BackupMigrate\Core\File\BackupFileInterface;
use BackupMigrate\Core\File\BackupFileReadableInterface;
use BackupMigrate\Core\Plugin\PluginBase;

/**
 * Class StreamDestination
 * @package BackupMigrate\Core\Destination
 */
class StreamDestination extends PluginBase implements WritableDestinationInterface, ConfigurableInterface {

  /**
   * {@inheritdoc}
   */
  function saveFile(BackupFileReadableInterface $file) {
    $stream_uri = $this->confGet('streamuri');
    if ($fp_out = fopen($stream_uri, 'w')) {
      $file->openForRead();
      while ($data = $file->readBytes(1024 * 512)) {
        fwrite($fp_out, $data);
      }
      fclose($fp_out);
      $file->close();
    }
    else {
      throw new \Exception("Cannot open the file $stream_uri for writing");
    }
  }

  /**
   * {@inheritdoc}
   */
  public function checkWritable() {
    $stream_uri = $this->confGet('streamuri');

    // The stream must exist.
    if (!file_exists($stream_uri)) {
      throw new DestinationNotWritableException('The file stream !uri does not exist.', ['%uri' => $stream_uri]);
    }

    // The stream must be writable.
    if (!file_exists($stream_uri)) {
      throw new DestinationNotWritableException('The file stream !uri cannot be written to.', ['%uri' => $stream_uri]);
    }
  }
  /**
   * {@inheritdoc}
   */
  public function getFile($id) {
    return NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function loadFileMetadata(BackupFileInterface $file) {
    return $file;
  }

  /**
   * {@inheritdoc}
   */
  public function loadFileForReading(BackupFileInterface $file) {
    return $file;
  }
}
