<?php
/**
 * @file
 */

use \BackupMigrate\Core\Config\ConfigBase;

/**
 * @coversDefaultClass \BackupMigrate\Core\Services\ConfigBase
 */
class ConfigBaseTest extends PHPUnit_Framework_TestCase {


  /**
   * @covers ::get
   * @covers ::set
   */
  public function testSettingGetting() {
    $conf = new ConfigBase();
    $conf->set('a', 'b');
    $this->assertEquals('b', $conf->get('a'));
    $conf->set('c', 'd');
    $this->assertEquals('b', $conf->get('a'));
    $this->assertEquals('d', $conf->get('c'));
  }

  /**
   * @covers ::__constructor
   */
  public function testConstructor() {
    $conf = new ConfigBase(
      array(
        'a' => 'b',
        'c' => 'd',
        ));
    $this->assertEquals('b', $conf->get('a'));
    $this->assertEquals('d', $conf->get('c'));

    $conf2 = new ConfigBase($conf);
    $this->assertEquals('b', $conf2->get('a'));
    $this->assertEquals('d', $conf2->get('c'));
  }

  /**
   * @covers ::fromArray()
   */
  public function testFromArray() {
    $conf = new ConfigBase();
    $conf->fromArray(array(
      'a' => 'b',
      'c' => 'd',
    ));
    $this->assertEquals('b', $conf->get('a'));
    $this->assertEquals('d', $conf->get('c'));
  }

  /**
   * @covers ::toArray()
   */
  public function testToArray() {
    $conf = new ConfigBase();
    $conf->set('a', 'b');
    $conf->set('c', 'd');
    $this->assertEquals(array(
      'a' => 'b',
      'c' => 'd',
    ), $conf->toArray());
  }
}
