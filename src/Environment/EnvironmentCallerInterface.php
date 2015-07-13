<?php
/**
 * @file
 */

namespace BackupMigrate\Core\Environment;


/**
 * Can be used by a plugin that needs to interact with one of the services in the
 * outside environment. If a plugin has this interface it will have the environment
 * automatically injected when prepared by the PluginManager.
 *
 * This should possibly be replaced with individual Interfaces/Traits for each
 * of the services provided by the Environment.
 *
 * Interface EnvironmentCallerInterface
 * @package BackupMigrate\Core\Environment
 */
interface EnvironmentCallerInterface {

  /**
   * Inject the environment.
   *
   * @param \BackupMigrate\Core\Environment\EnvironmentInterface $env
   * @return
   */
  public function setEnvironment(EnvironmentInterface $env);

  /**
   * Get the environment.

   * @return \BackupMigrate\Core\Environment\EnvironmentInterface
   */
  public function env();

}