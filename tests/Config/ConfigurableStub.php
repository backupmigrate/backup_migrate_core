<?php
/**
 * @file
 * Contains BackupMigrate\Core\Tests\Config\ConfigurableStub
 */


namespace BackupMigrate\Core\Tests\Config;


use BackupMigrate\Core\Config\Config;
use BackupMigrate\Core\Config\ConfigurableTrait;

/**
 * Class ConfigurableStub
 * @package BackupMigrate\Core\Tests\Config
 */
class ConfigurableStub {
  use ConfigurableTrait;

  /**
   * @return \BackupMigrate\Core\Config\Config
   */
  public function configDefaults() {
    return new Config(['a' => 'a-default', 'b' => 'b-default']);
  }
}