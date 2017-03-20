<?php

namespace BackupMigrate\Core\Destination;

use BackupMigrate\Core\Config\ConfigurableInterface;
use BackupMigrate\Core\Exception\BackupMigrateException;
use BackupMigrate\Core\File\BackupFile;
use BackupMigrate\Core\File\BackupFileInterface;
use BackupMigrate\Core\File\BackupFileReadableInterface;
use BackupMigrate\Core\Service\NodeSquirrelClient;

/**
 * Class NodeSquirrelDestination
 * @package BackupMigrate\Core\Destination
 */
class NodeSquirrelDestination extends DestinationBase implements RemoteDestinationInterface, ListableDestinationInterface, ReadableDestinationInterface, ConfigurableInterface {

  protected $client = null;

  /**
   * @var string[]
   */
  protected $api_endpoint = [];


  /**
   * @var HttpClientInterface
   */
  protected $http_client = null;


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
      $schema['fields']['secret_key'] = [
        'type' => 'text',
        'title' => $this->t('Secret Key'),
      ];
    }

    return $schema;
  }

  /**
   * {@inheritdoc}
   */
  public function checkWritable() {
    return true;
  }

  /**
   * Do the actual delete for a file.
   *
   * @param string $id The id of the file to delete.
   */
  protected function _deleteFile($id) {
    $this->getClient()->deleteFile($id);
  }

  /**
   * Do the actual file save. Should take care of the actual creation of a file
   * in the destination without regard for metadata.
   * @param \BackupMigrate\Core\File\BackupFileReadableInterface $file
   * @return null
   */
  protected function _saveFile(BackupFileReadableInterface $file) {
    $this->getClient()->uploadFile($file);
  }

  /**
   * Do the metadata save.
   *
   * @param \BackupMigrate\Core\File\BackupFileInterface $file
   */
  protected function _saveFileMetadata(BackupFileInterface $file) {
    // Metadata is saved during the file upload process. Nothing to do here.
  }

  /**
   * Load the actual metadata for the file
   *
   * @param \BackupMigrate\Core\File\BackupFileInterface $file
   */
  protected function _loadFileMetadataArray(BackupFileInterface $file) {
    // Metadata is fetched with the listing. There is no more to be fetched.
  }

  /**
   * Get a file object representing the file with the given ID from the destination.
   * This file item will not necessarily be readable nor will it have extended
   * metadata loaded. Use loadForReading and loadFileMetadata to get those.
   *
   * @TODO: Decide if extended metadata should ALWAYS be loaded here.
   *
   * @param string $id The unique identifier for the file. Usually the filename.
   *
   * @return \BackupMigrate\Core\File\BackupFileInterface
   *    The file if it exists or NULL if it doesn't
   */
  public function getFile($id) {
    // There is no way to fetch file info for a single file so we load them all.
    $files = $this->listFiles();
    if (isset($files[$id])) {
      return $files[$id];
    }
    return null;
  }

  /**
   * Load the file with the given ID from the destination.
   *
   * @param \BackupMigrate\Core\File\BackupFileInterface $file
   * @return \BackupMigrate\Core\File\BackupFileReadableInterface The file if it exists or NULL if it doesn't
   */
  public function loadFileForReading(BackupFileInterface $file) {
    // TODO: Implement loadFileForReading() method.
  }

  /**
   * Does the file with the given id (filename) exist in this destination.
   *
   * @param string $id The id (usually the filename) of the file.
   *
   * @return bool True if the file exists, false if it does not.
   */
  public function fileExists($id) {
    return (boolean)$this->getFile($id);
  }

  /**
   * Return a list of files from the destination. This list should be
   * date ordered from newest to oldest.
   *
   * @param integer $count The number of files to return.
   * @param integer $start The number to start at for pagination.
   *
   * @return BackupFileInterface[]
   *         An array of BackupFileInterface objects representing the files with
   *         the file ids as keys. The file ids are usually file names but that
   *         is up to the implementing destination to decide. The returned files
   *         may not be readable. Use loadFileForReading to get a readable file.
   */
  public function listFiles($count = 100, $start = 0) {
    $file_list = $this->getClient()->listFiles();

    $files = [];
    foreach ((array)$file_list as $file) {
        $out = new BackupFile();
        $out->setMeta('id', $file['filename']);
        $out->setMetaMultiple($file);
        $out->setFullName($file['filename']);
        $files[$file['filename']] = $out;
    }
    return $files;
  }

  /**
   * @return int The number of files in the destination.
   */
  public function countFiles() {
    $file_list = $this->getClient()->listBackups();
    return count($file_list);
  }

  /**
   * Get the client class.
   * @return \BackupMigrate\Core\Service\NodeSquirrelClient|null
   * @throws \BackupMigrate\Core\Exception\BackupMigrateException
   */
  protected function getClient() {
    if ($this->client == null) {
      $secret = $this->confGet('secret_key');
      if (!$secret) {
        throw new BackupMigrateException('You must enter a secret key in order to use NodeSquirrel.');
      }
      $this->client = new NodeSquirrelClient(
        $this->confGet('secret_key'),
        $this->confGet('api_endpoints', ['api.nodesquirrel.com'])
      );
    }
    return $this->client;
  }

  /**
   * Inject the client helper class.
   *
   * @param \BackupMigrate\Core\Service\NodeSquirrelClient $client
   */
  public function setNodeSquirrelClient(NodeSquirrelClient $client) {
    $this->client = $client;
  }
}
