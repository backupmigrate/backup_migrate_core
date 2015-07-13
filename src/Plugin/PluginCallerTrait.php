<?php
/**
 * @file
 * Contains BackupMigrate\Core\Plugin\PluginCallerTrait
 */


namespace BackupMigrate\Core\Plugin;


use BackupMigrate\Core\Plugin\PluginManagerInterface;

/**
 * Class PluginCallerTrait
 * @package BackupMigrate\Core\Plugin
 *
 * Implements the injection code for a PluginCallerInterface object.
 */
trait PluginCallerTrait {

  /**
   * @var \BackupMigrate\Core\Plugin\PluginManagerInterface;
   */
  protected $plugins;

  /**
   * Inject the plugin manager.
   *
   * @param \BackupMigrate\Core\Plugin\PluginManagerInterface $plugins
   */
  public function setPluginManager(PluginManagerInterface $plugins) {
    $this->plugins = $plugins;
  }

  /**
   * Get the plugin manager.
   * @return \BackupMigrate\Core\Plugin\PluginManagerInterface
   */
  public function plugins() {
    // Return the list of plugins or a blank placeholder.
    return $this->plugins ? $this->plugins : new PluginManager();
  }
}