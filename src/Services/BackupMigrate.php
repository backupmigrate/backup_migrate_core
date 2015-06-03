<?php

/**
 * @file
 * Contains \BackupMigrate\Core\Services\BackupMigrate.
 */

namespace BackupMigrate\Core\Services;

use \BackupMigrate\Core\Source;

/**
 * The core Backup and Migrate service.
 */
class BackupMigrate
{
  /**
   * A source manager listing the available sources.
   * 
   * @var \BackupMigrate\Core\Source\SourceManagerInterface
   */
  protected $sources;

  /**
   * Add a source manager
   * 
   * @param \BackupMigrate\Core\Source\SourceInterface $source 
   *    The source to add.
   * @param string $source_id
   *   Identifier of the provider.
   * @param int $weight
   *   (optional) The the order of the source when it appears in lists.
   */
  public function addSourceManager(SourceManagerInterface $sources) {
    $this->sources = $sources;
  }

  /**
   * Add an available source
   * 
   * @param \BackupMigrate\Core\Source\SourceInterface $source 
   *    The source to add.
   * @param string $source_id
   *   Identifier of the provider.
   * @param int $weight
   *   (optional) The the order of the source when it appears in lists.
   */
  public function addSource(SourceInterface $source, $source_id, $weight = 0) {
    if (!$this->sources) {
      $this->addSourceManager(new SourceManager());
    }
    $this->sources->addSource($source, $source_id, $weight);
  }


  /*
  
  // Backup pseudocode
  public function backup() {
    
    // Dependencies:
    // Config (specifies source id, dest id and the config for those and all plugins)
    // TempFileManager (so plugins can create temp files as needed)
    // Source/SourceManager
    // Destination/DestinationManager

    // Get the config from somewhere
    $config = new Config();

    // Get a list of the plugins from somewhere
    // Configure each plugin with the config object
    $plugins = new PluginManager($config);

    // Dependency injected file manager
    $filemanager = new TempFileManager();

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



   */


}
