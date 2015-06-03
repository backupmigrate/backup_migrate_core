<?php

/**
 * @file
 * Contains \BackupMigrate\Core\Plugin\PluginManagerInterface.
 */

namespace BackupMigrate\Core\Plugin;

use \BackupMigrate\Core\Config\ConfigInterface;

/**
 * Manage all of the available Plugins.
 */
interface PluginManagerInterface
{

  /**
   * Set the configuration for all plugins.
   * 
   * @param ConfigInterface $config A configuration object containing only configuration for all plugins
   */
  public function setConfig(ConfigInterface $config);

  /**
   * Add an available Plugin
   *
   * @param \BackupMigrate\Core\Plugin\PluginInterface $plugin
   * @param string $plugin_id Identifier of the provider.
   * @return
   */
  public function addPlugin(PluginInterface $plugin, $plugin_id);

  /**
   * Get the Plugin with the given id.
   *
   * @param $plugin_id string The id of the Plugin to return
   * @return \BackupMigrate\Core\Plugin\PluginInterface The Plugin specified by the id or NULL if it doesn't exist.
   */
  public function getPlugin($plugin_id);

  /**
   * Get a list of all of the Plugins.
   *
   * @return array An ordered list of the Plugins, keyed by their id.
   */
  public function getAllPlugins();
}
