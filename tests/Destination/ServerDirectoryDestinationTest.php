<?php
/**
 * @file
 */

use \BackupMigrate\Core\Destination\ServerDirectoryDestination;
use \BackupMigrate\Core\Config\Config;
use BackupMigrate\Core\Services\TempFileAdapter;
use BackupMigrate\Core\Services\TempFileManager;
use org\bovigo\vfs\vfsStream;


/**
 * @coversDefaultClass \BackupMigrate\Core\Destination\ServerDirectoryDestination
 */
class ServerDirectoryDestinationTest extends \PHPUnit_Framework_TestCase
{

  /**
   * @var string A URI for a virtual file
   */
  protected $destURI;

  /**
   * @var ServerDirectoryDestination
   */
  protected $destination;

  /**
   * {@inheritdoc}
   */
  public function setUp()
  {
    vfsStream::setup('tmp');
    vfsStream::setup('destination');
    vfsStream::create(['item1.txt' => 'Hello, World 1!']);
    vfsStream::create(['item2.txt' => 'Hello, World 2!']);
    vfsStream::create(['item3.txt' => 'Hello, World 3!']);
    $this->destURI = 'vfs://destination/';

    $this->destination = new ServerDirectoryDestination(new Config(['directory' => $this->destURI]));
  }

  /**
   * @covers ::countFiles
   */
  public function testCountFiles() {
    $files = $this->destination->countFiles();
    $this->assertEquals(3, $files);
  }

  /**
   * @covers ::listFiles
   */
  public function testListFiles() {
    $files = $this->destination->listFiles();
    $this->assertArrayHasKey('item1.txt', $files);
    $this->assertArrayHasKey('item2.txt', $files);
    $this->assertArrayHasKey('item3.txt', $files);

    // @TODO: test start and limit
    // @TODO: Test sort order.
  }

  /**
   * @covers ::loadFile
   */
  public function testLoad() {
    $file = $this->destination->loadFile('item1.txt');
    $this->assertInstanceOf('\BackupMigrate\Core\Util\BackupFileReadableInterface', $file);
    $this->assertEquals('Hello, World 1!', $file->read());
  }

  /**
   * @covers ::deleteFile
   */
  public function testDelete() {
    $file = $this->destination->deleteFile('item1.txt');
    $this->assertFileNotExists('vfs://destination/item1.txt');
    $this->assertFileExists('vfs://destination/item2.txt');
    $this->assertFileExists('vfs://destination/item3.txt');
  }

  /**
   * @covers ::saveFile
   */
  public function testSave() {
//    vfsStream::setup('tmp');
//    $manager = new TempFileManager(new TempFileAdapter('vfs://tmp/', 'abc'));
//    // Create with an extension.
//    $file = $manager->create('txt');
//    $file->write('Hello, World 4!');
//    $file->setMeta('filename', 'item4.txt');
//
//    $this->destination->saveFile($file);
//    // @TODO: make our TempFileAdapter work better with streams.
//    // Right now it returns a filepath even if you pass in a stream. Seems to be
//    // because tempnam is not stream aware.
//    // $this->assertFileNotExists('vfs://tmp/item4.txt');
//    $this->assertFileExists('vfs://destination/item1.txt');
//    $this->assertFileExists('vfs://destination/item2.txt');
//    $this->assertFileExists('vfs://destination/item3.txt');
//    $this->assertFileExists('vfs://destination/item4.txt');
//    $this->assertFileEquals('Hello, World 4!', 'vfs://destination/item4.txt');
  }

}
