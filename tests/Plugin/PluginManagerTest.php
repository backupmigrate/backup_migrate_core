<?php
/**
 * @file
 */

use BackupMigrate\Core\Config\Config;
use BackupMigrate\Core\Plugin\PluginManager;
use BackupMigrate\Core\Service\EnvironmentBase;

/**
 * Class PluginManagerTest
 */
class PluginManagerTest extends PHPUnit_Framework_TestCase {

  /**
   * @var PluginManager
   */
  protected $plugins;

  public function setUp() {
    $conf = new Config(
      [
        'test' => ['foo' => 'bar',],
        'test2' => ['foo' => 'baz', 'hello' => 'world']
      ]
    );
    $this->plugins = new PluginManager();
    $this->plugins->setConfig($conf);
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

    $this->plugins->add('test', $plugin);
    $this->plugins->add('test2', $plugin2);

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

    $this->plugins->add('test', $plugin);
    $this->plugins->add('test2', $plugin2);
  }

  /**
   * Test if configuration can be changed after initialization
   *
   * @covers ::add
   * @covers ::get
   * @covers ::setConfig
   */
  public function testReConfigure() {
    // Create a stub for the SomeClass class.
    $plugin = $this->getMockBuilder('\BackupMigrate\Core\Plugin\PluginBase')
      ->setMethods(['setConfig'])
      ->getMock();
    $plugin2 = $this->getMockBuilder('\BackupMigrate\Core\Plugin\PluginBase')
      ->setMethods(['setConfig'])
      ->getMock();

    $plugin->expects($this->at(0))
      ->method('setConfig')
      ->with(
        $this->equalTo(new Config(['foo' => 'bar']))
      );
    $plugin->expects($this->at(1))
      ->method('setConfig')
      ->with(
        $this->equalTo(new Config(['foo' => 'bar2']))
      );
    $plugin2->expects($this->at(0))
      ->method('setConfig')
      ->with(
        $this->equalTo(new Config(['foo' => 'baz', 'hello' => 'world']))
      );
    $plugin2->expects($this->at(1))
      ->method('setConfig')
      ->with(
        $this->equalTo(new Config(['foo' => 'baz2', 'hello' => 'planet!', 'abc' => 123]))
      );

    $this->plugins->add('test', $plugin);
    $this->plugins->add('test2', $plugin2);

    $this->plugins->setConfig(
      new Config([
        'test' => ['foo' => 'bar2',],
        'test2' => ['foo' => 'baz2', 'hello' => 'planet!', 'abc' => 123]
      ])
    );

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

    $this->plugins->add('test2', $plugin2);
    $this->plugins->add('test', $plugin);

    $op1 = $this->plugins->getAllByOp('op1');
    $this->assertEquals([$plugin2, $plugin], array_values($op1));

    $op2 = $this->plugins->getAllByOp('op2');
    $this->assertEquals([$plugin, $plugin2], array_values($op2));

    $op3 = $this->plugins->getAllByOp('op3');
    $this->assertEquals([$plugin2], array_values($op3));
  }
}
