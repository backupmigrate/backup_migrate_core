<?php
/**
 * @file
 */

use BackupMigrate\Core\Config\Config;
use BackupMigrate\Core\Plugin\PluginManager;
use \BackupMigrate\Core\Services\EnvironmentBase;

/**
 * Class PluginManagerTest
 */
class PluginManagerTest extends PHPUnit_Framework_TestCase {

  /**
   * @var PluginManager
   */
  protected $plugins;

  public function setUp() {
    $env = new \BackupMigrate\Core\Services\EnvironmentBase();
    $conf = new Config(
      [
        'test' => ['foo' => 'bar',],
        'test2' => ['foo' => 'baz', 'hello' => 'world']
      ]
    );
    $this->plugins = new PluginManager($env, $conf);
  }

  /**
   * @covers ::add
   * @covers ::get
   * @covers ::getAll
   */
  public function testAdd() {
    // Create a stub for the plugin class.
    $plugin = $this->getMockBuilder('\BackupMigrate\Core\Plugin\PluginBase')
      ->getMock();
    $plugin2 = $this->getMockBuilder('\BackupMigrate\Core\Plugin\PluginBase')
      ->getMock();

    $this->plugins->add($plugin, 'test');
    $this->plugins->add($plugin2, 'test2');

    $this->assertEquals($plugin, $this->plugins->get('test'));
    $this->assertEquals($plugin2, $this->plugins->get('test2'));

    $this->assertEquals(['test' => $plugin, 'test2' => $plugin2], $this->plugins->getAll());
  }

  /**
   * @covers ::add
   * @covers ::get
   */
  public function testConfigure() {
    // Create a stub for the SomeClass class.
    $plugin = $this->getMockBuilder('\BackupMigrate\Core\Plugin\PluginBase')
      ->setMethods(['setConfig'])
      ->getMock();
    $plugin2 = $this->getMockBuilder('\BackupMigrate\Core\Plugin\PluginBase')
      ->setMethods(['setConfig'])
      ->getMock();


    $plugin->expects($this->once())->method('setConfig')->with(
      $this->equalTo(new Config(['foo' => 'bar']))
    );
    $plugin2->expects($this->once())->method('setConfig')->with(
      $this->equalTo(new Config(['foo' => 'baz', 'hello' => 'world']))
    );

    $this->plugins->add($plugin, 'test');
    $this->plugins->add($plugin2, 'test2');
  }


  /**
   * @covers ::add
   * @covers ::get
   */
  public function testGetAllByOp() {
    // Create a stub for the SomeClass class.
    $plugin = $this->getMockBuilder('\BackupMigrate\Core\Plugin\PluginBase')
      ->setMethods(['supportedOps'])
      ->getMock();
    $plugin2 = $this->getMockBuilder('\BackupMigrate\Core\Plugin\PluginBase')
      ->setMethods(['supportedOps'])
      ->getMock();

    $plugin->method('supportedOps')->willReturn(
      ['op1' => ['weight' => 100], 'op2' => ['weight' => -100]]
    );
    $plugin2->method('supportedOps')->willReturn(
      ['op1' => ['weight' => -100], 'op2' => ['weight' => 100], 'op3' => []]
    );

    $this->plugins->add($plugin2, 'test2');
    $this->plugins->add($plugin, 'test');

    $op1 = $this->plugins->getAllByOp('op1');
    $this->assertEquals([$plugin2, $plugin], $op1);

    $op2 = $this->plugins->getAllByOp('op2');
    $this->assertEquals([$plugin, $plugin2], $op2);

    $op3 = $this->plugins->getAllByOp('op3');
    $this->assertEquals([$plugin2], $op3);
  }
}