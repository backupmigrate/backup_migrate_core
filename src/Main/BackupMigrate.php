<?php

/**
 * @file
 * Contains \BackupMigrate\Core\Services\BackupMigrate.
 */

namespace BackupMigrate\Core\Main;

use BackupMigrate\Core\Config\ConfigInterface;
use BackupMigrate\Core\Plugin\PluginManagerInterface;
use BackupMigrate\Core\Exception\BackupMigrateException;
use BackupMigrate\Core\Plugin\PluginCallerInterface;
use BackupMigrate\Core\Plugin\PluginCallerTrait;
use BackupMigrate\Core\Plugin\PluginManager;
use BackupMigrate\Core\Service\ServiceManager;

/**
 * The core Backup and Migrate service.
 */
class BackupMigrate implements BackupMigrateInterface
{
  use PluginCallerTrait;

  /**
   * @var \BackupMigrate\Core\Plugin\PluginManagerInterface;
   */
  protected $sources;

  /**
   * @var \BackupMigrate\Core\Plugin\PluginManagerInterface;
   */
  protected $destinations;

  /**
   * @var ServiceManager The service locator for this object.
   */
  protected $services;

  /**
   * {@inheritdoc}
   * @param \BackupMigrate\Core\Config\ConfigInterface $config
   * @param \BackupMigrate\Core\Service\ServiceManagerInterface $services
   */
  function __construct() {
    $this->setServiceManager(new ServiceManager());
    $services = $this->services();

    $services->add('PluginManager', new PluginManager($services));
    $services->add('SourceManager', new PluginManager($services));
    $services->add('DestinationManager', new PluginManager($services));

    // Add these services back into this object using the service manager.
    $services->addClient($this);
  }

  /**
   * {@inheritdoc}
   */
  public function backup($source_id, $destination_id) {
    try {

      // Allow the plugins to set up.
      $this->plugins()->call('setUp', 'backup', $source_id, $destination_id);

      // Get the source and the destination to use.
      $source = $this->sources()->get($source_id);
      $destinations = array();

      // Allow a single destination or multiple destinations.
      foreach ((array)$destination_id as $id) {
        $destinations[$id] = $this->destinations()->get($id);

        // Check that the destination is valid.
        if (!$destinations[$id]) {
          throw new BackupMigrateException('The destination !id does not exist.', array('!id' => $destination_id));
        }

        // Check that the destination can be written to.
        // @TODO: Catch exceptions and continue if at least one destination is valid.
        $destinations[$id]->checkWritable();
      }

      // Check that the source is valid.
      if (!$source) {
        throw new BackupMigrateException('The source !id does not exist.', array('!id' => $source_id));
      }

      // Run each of the installed plugins which implements the 'beforeBackup' operation.
      $this->plugins()->call('beforeBackup');

      // Do the actual backup.
      $file = $source->exportToFile();

      // Run each of the installed plugins which implements the 'afterBackup' operation.
      $file = $this->plugins()->call('afterBackup', $file);

      // Save the file to each destination.
      foreach ($destinations as $destination) {
        $destination->saveFile($file);
      }

      // Let plugins react to a successful operation.
      $this->plugins()->call('backupSucceed', $file);
    }
    catch (\Exception $e) {
      // Let plugins react to a failed operation.
      $this->plugins()->call('backupFail', $e);

      // The consuming software needs to deal with this.
      throw $e;
    }

    // Allow the plugins to tear down.
    $this->plugins()->call('tearDown', 'backup', $source_id, $destination_id);

  }

  /**
   * {@inheritdoc}
   */
  public function restore($source_id, $destination_id, $file_id = NULL) {
    try {
      // Get the source and the destination to use.
      $source = $this->sources()->get($source_id);
      $destination = $this->destinations()->get($destination_id);

      if (!$source) {
        throw new BackupMigrateException('The source !id does not exist.', array('!id' => $source_id));
      }
      if (!$destination) {
        throw new BackupMigrateException('The destination !id does not exist.', array('!id' => $destination_id));
      }

      // Load the file from the destination.
      $file = $destination->getFile($file_id);
      if (!$file) {
        throw new BackupMigrateException('The file !id does not exist.', array('!id' => $file_id));
      }

      // Prepare the file for reading.
      $file = $destination->loadFileForReading($file);
      if (!$file) {
        throw new BackupMigrateException('The file !id could not be opened for reading.', array('!id' => $file_id));
      }

      // Run each of the installed plugins which implements the 'backup' operation.
      $file = $this->plugins()->call('beforeRestore', $file);

      // Do the actual source restore.
      $source->importFromFile($file);

      // Run each of the installed plugins which implements the 'beforeBackup' operation.
      $this->plugins()->call('afterRestore');

      // Let plugins react to a successful operation.
      $this->plugins()->call('restoreSucceed', $file);
    }
    catch (\Exception $e) {
      // Let plugins react to a failed operation.
      $this->plugins()->call('restoreFail', $e);

      // The consuming software needs to deal with this.
      throw $e;
    }
  }

  /**
   * Set the configuration for the service. This simply passes the configuration
   * on to the plugin manager as all work is done by plugins.
   *
   * This can be called after the service is instantiated to pass new configuration
   * to the plugins.
   *
   * @param \BackupMigrate\Core\Config\ConfigInterface $config
   */
  public function setConfig(ConfigInterface $config) {
    $this->plugins()->setConfig($config);
  }

  /**
   * Get the list of available destinations.
   *
   * @return PluginManagerInterface
   */
  public function destinations() {
    return $this->destinations;
  }

  /**
   * Set the destinations plugin manager.
   *
   * @param PluginManagerInterface $destinations
   */
  public function setDestinationManager(PluginManagerInterface $destinations) {
    $this->destinations = $destinations;
  }

  /**
   * Get the list of sources.
   *
   * @return PluginManagerInterface
   */
  public function sources() {
    return $this->sources;
  }

  /**
   * Set the sources plugin manager.
   *
   * @param PluginManagerInterface $sources
   */
  public function setSourceManager(PluginManagerInterface $sources) {
    $this->sources = $sources;
  }

  /**
   * Get the service locator.
   *
   * @return ServiceManager
   */
  public function services() {
    return $this->services;
  }

  /**
   * Set the service locator.
   *
   * @param ServiceManager $services
   */
  public function setServiceManager($services) {
    $this->services = $services;
  }
}
