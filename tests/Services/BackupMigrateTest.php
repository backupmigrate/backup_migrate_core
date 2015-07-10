<?php
/**
 * @file
 */

namespace BackupMigrate\Core\Tests\Services;
use BackupMigrate\Core\Config\Config;
use BackupMigrate\Core\Plugin\PluginManager;
use BackupMigrate\Core\Services\BackupMigrate;
use BackupMigrate\Core\Tests\TempFileConsumerTestTrait;
use BackupMigrate\Core\Util\ReadableStreamBackupFile;


/**
 * Class BackupMigrateTest
 * @package BackupMigrate\Core\Tests\Services
 */
class BackupMigrateTest extends \PHPUnit_Framework_TestCase {
  use TempFileConsumerTestTrait;

  /**
   * @var PluginManager
   */
  protected $plugins;

  protected $bam;

  public function setUp() {
    $env = new \BackupMigrate\Core\Environment\EnvironmentBase();
    $conf = new Config(
      [
        'test' => ['foo' => 'bar',],
        'test2' => ['foo' => 'baz', 'hello' => 'world']
      ]
    );
    $this->_setUpFiles(['file.txt' => 'Hello, World!']);

    $this->bam = new BackupMigrate($env, $conf);
  }

  /**
   * Test backing up from a source to a destination.
   */
  public function testAddPlugin() {
    // Create a stub for the plugin class.
    $plugin = $this->getMockBuilder('\BackupMigrate\Core\Plugin\PluginBase')
      ->getMock();
    $plugin2 = $this->getMockBuilder('\BackupMigrate\Core\Plugin\PluginBase')
      ->getMock();

    $this->bam->plugins()->add($plugin, 'test');
    $this->bam->plugins()->add($plugin2, 'test2');

    $this->assertEquals($plugin, $this->bam->plugins()->get('test'));
    $this->assertEquals($plugin2, $this->bam->plugins()->get('test2'));

    $this->assertEquals(['test' => $plugin, 'test2' => $plugin2], $this->bam->plugins()->getAll());

  }

  /**
   * Test backing up from a source to a destination.
   */
  public function testBackup() {
    $file = new ReadableStreamBackupFile('vfs://root/file.txt');

    $source = $this->getMockBuilder('\BackupMigrate\Core\Source\SourceBase')
      ->setMethods(['exportToFile', 'importFromFile'])
      ->getMock();
    $source->method('exportToFile')->willReturn($file);

    $plugin = $this->getMockBuilder('\BackupMigrate\Core\Plugin\PluginBase')
      ->setMethods(['afterBackup', 'supportedOps'])
      ->getMock();
    $plugin->method('supportedOps')->willReturn(
      ['afterBackup' => ['weight' => 100]]
    );
    $plugin->method('afterBackup')->willReturn($file);

    $destination = $this->getMockBuilder('\BackupMigrate\Core\Destination\DirectoryDestination')
      ->setMethods(['saveFile'])
      ->getMock();

    $destination->expects($this->once())->method('saveFile')->with(
      $this->equalTo($file)
    );
    $plugin->expects($this->once())->method('afterBackup')->with(
      $this->equalTo($file)
    );

    $this->bam->plugins()->add($source, 'source');
    $this->bam->plugins()->add($destination, 'destination');
    $this->bam->plugins()->add($plugin, 'test');

    $this->bam->backup('source', 'destination');
  }

  /**
   * Test backing up from a source to a destination.
   */
  public function testRestore() {
    $file = new ReadableStreamBackupFile('vfs://root/file.txt');

    $source = $this->getMockBuilder('\BackupMigrate\Core\Source\SourceBase')
      ->setMethods(['exportToFile', 'importFromFile'])
      ->getMock();

    $plugin = $this->getMockBuilder('\BackupMigrate\Core\Plugin\PluginBase')
      ->setMethods(['beforeRestore', 'supportedOps'])
      ->getMock();
    $plugin->method('supportedOps')->willReturn(
      ['beforeRestore' => ['weight' => 100]]
    );
    $plugin->method('beforeRestore')->willReturn($file);

    $destination = $this->getMockBuilder('\BackupMigrate\Core\Destination\DirectoryDestination')
      ->setMethods(['getFile'])
      ->getMock();
    $destination->method('getFile')->willReturn($file);

    $destination->expects($this->once())->method('getFile')->with(
      $this->equalTo('file.txt')
    );
    $plugin->expects($this->once())->method('beforeRestore')->with(
      $this->equalTo($file)
    );
    $source->expects($this->once())->method('importFromFile')->with(
      $this->equalTo($file)
    );

    $this->bam->plugins()->add($source, 'source');
    $this->bam->plugins()->add($destination, 'destination');
    $this->bam->plugins()->add($plugin, 'test');

    $this->bam->restore('source', 'destination', 'file.txt');
  }
}
