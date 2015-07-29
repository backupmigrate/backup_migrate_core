<?php
/**
 * @file
 */

use BackupMigrate\Core\Config\Config;
use BackupMigrate\Core\Tests\Config\ConfigurableStub;

/**
 * @coversDefaultTrait \BackupMigrate\Core\Config\ConfigurableTrait
 */
class ConfigurableTraitTest extends PHPUnit_Framework_TestCase {

  /**
   * @covers ::__constructor
   */
  public function testConstructor() {
    // Blank configurable (with defaults)
    $configurable = new ConfigurableStub();

    $this->assertEquals('a-default', $configurable->confGet('a'));
    $this->assertEquals('b-default', $configurable->confGet('b'));
    $this->assertEmpty($configurable->confGet('c'));

    // Configurable with initial state (override 'a' default)
    $configurable = new ConfigurableStub(new Config(['a' => 'a-init', 'c' => 'c-init']));

    $this->assertEquals('a-init', $configurable->confGet('a'));
    $this->assertEquals('b-default', $configurable->confGet('b'));
    $this->assertEquals('c-init', $configurable->confGet('c'));

    // Override everything
    $configurable->setConfig(new Config([
      'a' => 'a-config',
      'b' => 'b-config',
      'c' => 'c-config'
    ]));
    $this->assertEquals('a-config', $configurable->confGet('a'));
    $this->assertEquals('b-config', $configurable->confGet('b'));
    $this->assertEquals('c-config', $configurable->confGet('c'));

    // Override initial value (everything else resets to init/defaults)
    $configurable->setConfig(new Config([
      'a' => 'a-config2',
    ]));
    $this->assertEquals('a-config2', $configurable->confGet('a'));
    $this->assertEquals('b-default', $configurable->confGet('b'));
    $this->assertEquals('c-init', $configurable->confGet('c'));

    // Override default value
    $configurable->setConfig(new Config([
      'b' => 'b-config2',
    ]));
    $this->assertEquals('a-init', $configurable->confGet('a'));
    $this->assertEquals('b-config2', $configurable->confGet('b'));
    $this->assertEquals('c-init', $configurable->confGet('c'));

    // Reset everything
    $configurable->setConfig(new Config([]));
    $this->assertEquals('a-init', $configurable->confGet('a'));
    $this->assertEquals('b-default', $configurable->confGet('b'));
    $this->assertEquals('c-init', $configurable->confGet('c'));

  }
}
