<?php
/**
 * @file
 * Contains BackupMigrate\Core\Destination\StreamDestination
 */


namespace BackupMigrate\Core\Destination;


use BackupMigrate\Core\Config\ConfigInterface;
use BackupMigrate\Core\Config\ConfigurableInterface;
use BackupMigrate\Core\Config\ConfigurableTrait;
use BackupMigrate\Core\Util\BackupFileInterface;
use BackupMigrate\Core\Util\BackupFileReadableInterface;
use BackupMigrate\Core\Plugin\PluginBase;

/**
 * Class StreamDestination
 * @package BackupMigrate\Core\Destination
 */
class StreamDestination extends PluginBase implements DestinationInterface, ConfigurableInterface {

  /**
   * Get a list of supported operations and their weight.
   *
   * @return array
   */
  public function supportedOps() {
    return [];
  }

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

  /**
   * {@inheritdoc}
   */
  public function listFiles($count = 100, $start = 0) {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function countFiles() {
    return 0;
  }

  /**
   * {@inheritdoc}
   */
  public function fileExists($id) {
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function deleteFile($id) {
  }

}