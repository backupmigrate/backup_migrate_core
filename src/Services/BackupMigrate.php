<?php

/**
 * @file
 * Contains \BackupMigrate\Core\Services\BackupMigrate.
 */

namespace BackupMigrate\Core\Services;

use BackupMigrate\Core\Config\ConfigBase;
use \BackupMigrate\Core\Config\ConfigInterface;
use \BackupMigrate\Core\Destination\DestinationManagerInterface;
use \BackupMigrate\Core\Plugin\PluginManagerInterface;
use \BackupMigrate\Core\Source\SourceManagerInterface;

/**
 * The core Backup and Migrate service.
 */
class BackupMigrate implements BackupMigrateInterface
{
  /**
   * A source manager listing the available sources.
   * 
   * @var \BackupMigrate\Core\Source\SourceManagerInterface
   */
  protected $sources;

  /**
   * A destination manager listing the available destinations.
   *
   * @var \BackupMigrate\Core\Destination\DestinationManagerInterface
   */
  protected $destinations;

  /**
   * @var \BackupMigrate\Core\Plugin\PluginManagerInterface
   */
  protected $plugins;

  /**
   * The interface with the underlying app
   *
   * @var \BackupMigrate\Core\Services\ApplicationInterface
   */
  protected $app;

  /**
   * @var \BackupMigrate\Core\Config\ConfigInterface
   */
  protected $config;

  /**
   * {@inheritdoc}
   */
  function __construct(SourceManagerInterface $sources, DestinationManagerInterface $destinations, PluginManagerInterface $plugins, ApplicationInterface $app, ConfigInterface $config = NULL) {
    $this->sources = $sources;
    $this->destinations = $destinations;
    $this->plugins = $plugins;

    $this->app = $app;
    $this->config = empty($this->config) ? new ConfigBase() : $config;
  }

  /*

  // Backup pseudocode
  public function backup($source, $dest, $config) {

    // Dependencies:
    // Config (specifies source id, dest id and the config for those and all plugins)
    // TempFileManager (so plugins can create temp files as needed)
    // Source/SourceManager
    // Destination/DestinationManager
    // Application
    //  Cache
    //  State
    //  TempFileManager
    //

    // Get the config from somewhere. Passed in probably.
    $config = new Config();

    // Dependency injected file manager
    $filemanager = new TempFileManager();

    // Get a list of the plugins from somewhere
    // Configure each plugin with the config object
    $plugins = new PluginManager($sources, $destinations, $tempfilemanager, $config);

    // Basically we can make generating the file and saving the file separate operations:
    $file = $source->backup($config);
    foreach ($plugins->getPluginsByOp('backup') as $plugin) {
      $file = $plugins->backup($file, $filemanager);
    }
    $destination->save($file);
    /// Which is conceptually clean and allows filters to not know about dest/source
    // But requires some duplication on how config is handled

    // Or we can treat backing up and saving as the bookend filters in the chain
    // Which is more abstract and confusing and means injecting 2 of the plugins with
    // special managers OR with the source/dest themselves.

    // Generate a new Temp File. Needs injected manager.
    $file = $filemanager->new();
    // OR
    $file = NULL; // Make the first plugin create the file if it doesn't exist
    // This only works when the first plugin is the 'backup' plugin.


    // Each plugin operates on the file returing either the same temp file
    // or a new one.
    // Plugin example:
    //    1. BackupGenerator (requires source/source manager)
    //    2. Compressor
    //    3. FileName (adds timestamp etc.)
    //    4. MetadataAdder
    //    5. DestinationSaver (requires destination/destination manager)


    foreach ($plugins->getPluginsByOp('backup') as $plugin) {
      $file = $plugins->backup($file, $filemanager);
    }




  }

  Usage:
  $config = new ConfigBase(..)

  $bam = new BackupMigrate();

  // Autodiscovered (but dynamic)?
  $bam->addSource('db', new MySQLSource(...));
  $bam->addSource('files', new FileSource(...));
  $bam->addDestination('manual', new FileDestination(...)));

  // Autodiscovered?
  $bam->addPlugin('compression', new CompressionFilter());
  $bam->addPlugin('encryption', new EncryptionFilter());

  $bam->backup($from, $to, [$config]);

-- or --
  $config->set('source', $from);
  $config->set('destination', $to);
  $bam->backup($config);

-- or --
  $config->set('source', $from);
  $config->set('destination', $to);
  $bam->setConfig($config);
  $bam->backup();

-- or --
  $bam->setConfig($config);
  $bam->backup($from, $to);

  -- OR --
  $sources = new SourceManager();
  $sources->add(new MySQLSource(...), 'db');
  $sources->add(new MySQLSource(...), 'another');

  $destinations = new DestinationManager();
  $destinations->add(new DirectoryDestination(...), 'manual');

  $plugins = new PluginManager();
  $plugins->add(new CompressionPlugin(), 'encryption');

  $app = new Drupal8Application();

  $bam = new BackupMigrate($sources, $destinations, $plugins, $app, $config);
  $bam->backup($from, $to);
  */

  /**
   * {@inheritdoc}
   */
  public function backup($source_id, $destination_id) {
    // TODO: Implement backup() method.
  }

  /**
   * {@inheritdoc}
   */
  public function restore($source_id, $destination_id, $file = NULL) {
    // TODO: Implement restore() method.
  }
}
