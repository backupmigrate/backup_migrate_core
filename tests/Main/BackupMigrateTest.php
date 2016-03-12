<?php
/**
 * @file
 */

namespace BackupMigrate\Core\Tests\Main;

use BackupMigrate\Core\Config\Config;
use BackupMigrate\Core\Main\BackupMigrateInterface;
use BackupMigrate\Core\Plugin\PluginManager;
use BackupMigrate\Core\Main\BackupMigrate;
use BackupMigrate\Core\Plugin\PluginManagerInterface;
use BackupMigrate\Core\Tests\File\TempFileConsumerTestTrait;
use \BackupMigrate\Core\File\ReadableStreamBackupFile;


/**
 * Class BackupMigrateTest
 * @package BackupMigrate\Core\Tests\Services
 */
class BackupMigrateTest extends \PHPUnit_Framework_TestCase {
  use TempFileConsumerTestTrait;

  /**
   * @var PluginManagerInterface
   */
  protected $plugins;

  /**
   * @var BackupMigrate
   */
  protected $bam;

  public function setUp() {
    $conf = new Config(
      [
        'test' => ['foo' => 'bar',],
        'test2' => ['foo' => 'baz', 'hello' => 'world']
      ]
    );
    $this->_setUpFiles(['file.txt' => 'Hello, World!']);

    $this->bam = new BackupMigrate();
    $this->bam->setConfig($conf);
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

    $this->bam->plugins()->add('test', $plugin);
    $this->bam->plugins()->add('test2', $plugin2);

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
      ->setMethods(['saveFile', 'checkWritable'])
      ->getMock();
    $destination->method('checkWritable')->willReturn(true);


    $destination->expects($this->once())->method('checkWritable');
    $destination->expects($this->once())->method('saveFile')->with(
      $this->equalTo($file)
    );
    $destination2 = $this->getMockBuilder('\BackupMigrate\Core\Destination\DirectoryDestination')
      ->setMethods(['saveFile'])
      ->getMock();

    $destination2->expects($this->never())->method('saveFile')->with(
      $this->equalTo($file)
    );

    $plugin->expects($this->once())->method('afterBackup')->with(
      $this->equalTo($file)
    );

    $this->bam->sources()->add('source', $source);
    $this->bam->destinations()->add('destination', $destination);
    $this->bam->destinations()->add('destination2', $destination2);
    $this->bam->plugins()->add('test', $plugin);

    $this->bam->backup('source', 'destination');

  }

  /**
   * Test backing up from a source to a destination.
   */
  public function testBackupMultiple() {
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
      ->setMethods(['saveFile', 'checkWritable'])
      ->getMock();
    $destination->method('checkWritable')->willReturn(true);

    $destination->expects($this->once())->method('checkWritable');
    $destination->expects($this->once())->method('saveFile')->with(
      $this->equalTo($file)
    );
    $destination2 = $this->getMockBuilder('\BackupMigrate\Core\Destination\DirectoryDestination')
      ->setMethods(['saveFile', 'checkWritable'])
      ->getMock();
    $destination2->method('checkWritable')->willReturn(true);

    $destination2->expects($this->once())->method('checkWritable');
    $destination2->expects($this->once())->method('saveFile')->with(
      $this->equalTo($file)
    );

    $plugin->expects($this->once())->method('afterBackup')->with(
      $this->equalTo($file)
    );

    $this->bam->sources()->add('source', $source);
    $this->bam->destinations()->add('destination', $destination);
    $this->bam->destinations()->add('destination2', $destination2);
    $this->bam->plugins()->add('test', $plugin);

    $this->bam->backup('source', array('destination', 'destination2'));

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

    $this->bam->sources()->add('source', $source);
    $this->bam->destinations()->add('destination', $destination);
    $this->bam->plugins()->add('test', $plugin);

    $this->bam->restore('source', 'destination', 'file.txt');
  }
}
