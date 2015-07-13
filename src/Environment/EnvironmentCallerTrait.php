<?php
/**
 * @file
 */

namespace BackupMigrate\Core\Environment;


/**
 * Class EnvironmentCallerTrait
 * @package BackupMigrate\Core\Environment
 *
 * Implement the injection functionality of a plugin that interacts with
 * the outside environment (cache, logs, etc.).
 */
trait EnvironmentCallerTrait {

  /**
   * @var \BackupMigrate\Core\Environment\EnvironmentInterface
   */
  protected $env;

  /**
   * Inject the environment.
   *
   * @param \BackupMigrate\Core\Environment\EnvironmentInterface $env
   */
  public function setEnvironment(EnvironmentInterface $env) {
    $this->env = $env;
  }

  /**
   * Get the environment (essentially a dependency injection container for
   * interfacing with the broader consuming app and environment)
   *
   * @return \BackupMigrate\Core\Environment\EnvironmentInterface
   */
  public function env() {
    // Create a default Environment with mostly Null providers.
    if ($this->env == NULL) {
      $this->env = new EnvironmentBase();
    }
    return $this->env;
  }
}