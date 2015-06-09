<?php
/**
 * @file
 */

namespace BackupMigrate\Core\Plugin;


use BackupMigrate\Core\Plugin\PluginManagerInterface;

/**
 * Interface PluginCallerPluginInterface
 * @package BackupMigrate\Core\Plugin
 *
 * An interface for plugins which need to access other plugins and therefore
 * must have access to a plugin manager.
 */
interface PluginCallerInterface {

  /**
   * Inject the plugin manager.
   *
   * @param \BackupMigrate\Core\Plugin\PluginManagerInterface $plugins
   */
  public function setPluginManager(PluginManagerInterface $plugins);

  /**
   * Get the plugin manager.

   * @return \BackupMigrate\Core\Plugin\PluginManagerInterface
   */
  public function plugins();
}