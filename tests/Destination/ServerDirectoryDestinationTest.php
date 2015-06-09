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
   * @var TempFileManager
   */
  protected $manager;

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

    $this->manager = new TempFileManager(new TempFileAdapter('vfs://destination/', 'abc'));
    $this->destination->setTempFileManager($this->manager);
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
   * @covers ::loadFileForReading
   */
  public function testLoad() {
    $file = $this->destination->getFile('item1.txt');
    $this->assertInstanceOf('\BackupMigrate\Core\Util\BackupFileInterface', $file);
    $file = $this->destination->loadFileForReading($file);
    $this->assertInstanceOf('\BackupMigrate\Core\Util\BackupFileReadableInterface', $file);
    $this->assertEquals('Hello, World 1!', $file->read());
  }

  /**
   * @covers ::deleteFile
   */
  public function testDelete() {
    $this->destination->deleteFile('item1.txt');
    $this->assertFileNotExists('vfs://destination/item1.txt');
    $this->assertFileExists('vfs://destination/item2.txt');
    $this->assertFileExists('vfs://destination/item3.txt');
  }

  /**
   * @covers ::saveFile
   */
  public function testSave() {
    // Create with an extension.
    $file = $this->manager->create('txt');
    $file->write('Hello, World 4!');
    $file->setMeta('filename', 'item4.txt');

    $this->destination->saveFile($file);
    $this->assertFileExists('vfs://destination/item1.txt');
    $this->assertFileExists('vfs://destination/item2.txt');
    $this->assertFileExists('vfs://destination/item3.txt');
    $this->assertFileExists('vfs://destination/item4.txt');
    $this->assertEquals('Hello, World 4!', file_get_contents('vfs://destination/item4.txt'));
  }

  /**
   * @covers ::saveFile
   */
  public function testMetadata() {
    // Create with an extension.
    $file = $this->manager->create('txt');
    $file->write('Hello, World 4!');
    $file->setMeta('filename', 'item4.txt');
    $file->setMeta('x-example', '12345');

    $this->destination->saveFile($file);

    $this->assertFileExists('vfs://destination/item4.txt');
    $this->assertEquals('Hello, World 4!', file_get_contents('vfs://destination/item4.txt'));

    $file = $this->destination->getFile('item4.txt');
    $file = $this->destination->loadFileMetadata($file);
    $this->assertEquals('12345', $file->getMeta('x-example'));

    // Dipping beneath the API to test that the info file doesn't exist after a delete
    $this->destination->deleteFile('item4.txt');
    $this->assertFileNotExists('vfs://destination/item4.txt');
    $this->assertFileNotExists('vfs://destination/item4.txt.info');
  }

}
