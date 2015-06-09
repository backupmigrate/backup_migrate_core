<?php
/**
 * @file
 */

use \BackupMigrate\Core\Config\Config;

/**
 * @coversDefaultClass \BackupMigrate\Core\Services\ConfigObject
 */
class ConfigTest extends PHPUnit_Framework_TestCase {


  /**
   * @covers ::get
   * @covers ::set
   */
  public function testSettingGetting() {
    $conf = new Config();
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
    $conf = new Config(
      array(
        'a' => 'b',
        'c' => 'd',
        ));
    $this->assertEquals('b', $conf->get('a'));
    $this->assertEquals('d', $conf->get('c'));

    $conf2 = new Config($conf);
    $this->assertEquals('b', $conf2->get('a'));
    $this->assertEquals('d', $conf2->get('c'));
  }

  /**
   * @covers ::fromArray()
   */
  public function testFromArray() {
    $conf = new Config();
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
    $conf = new Config();
    $conf->set('a', 'b');
    $conf->set('c', 'd');
    $this->assertEquals(array(
      'a' => 'b',
      'c' => 'd',
    ), $conf->toArray());
  }
}
