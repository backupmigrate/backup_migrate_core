<?php
/**
 * @file
 */

use \BackupMigrate\Core\Destination\DirectoryDestination;
use \BackupMigrate\Core\Config\Config;
use BackupMigrate\Core\Tests\File\TempFileConsumerTestTrait;


/**
 * @coversDefaultClass \BackupMigrate\Core\Destination\DirectoryDestination
 */
class DirectoryDestinationTest extends \PHPUnit_Framework_TestCase
{
  use TempFileConsumerTestTrait;

  /**
   * @var string A URI for a virtual file
   */
  protected $destURI;

  /**
   * @var DirectoryDestination
   */
  protected $destination;


  /**
   * {@inheritdoc}
   */
  public function setUp()
  {
    $this->_setUpFiles([
      'tmp' => [],
      'files' => [
        'item1.txt' => 'Hello, World 1!',
        'item2.txt' => 'Hello, World 2!',
        'item3.txt' => 'Hello, World 3!',
      ]
    ]);

    $this->destURI = 'vfs://root/files/';

    $this->destination = new DirectoryDestination(new Config(['directory' => $this->destURI]));
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
    $this->assertInstanceOf('\BackupMigrate\Core\File\BackupFileInterface', $file);
    $file = $this->destination->loadFileForReading($file);
    $this->assertInstanceOf('\BackupMigrate\Core\File\BackupFileReadableInterface', $file);
    $this->assertEquals('Hello, World 1!', $file->readAll());
  }

  /**
   * @covers ::deleteFile
   */
  public function testDelete() {
    $this->assertFileExists($this->destURI . '/item1.txt');
    $this->destination->deleteFile('item1.txt');
    $this->assertFileNotExists($this->destURI . '/item1.txt');
    $this->assertFileExists($this->destURI . '/item2.txt');
    $this->assertFileExists($this->destURI . '/item3.txt');
  }

  /**
   * @covers ::saveFile
   */
  public function testSave() {
    // Create with an extension.
    $file = $this->manager->create('txt');
    $file->write('Hello, World 4!');
    $file->setName('item4');

    $this->destination->saveFile($file);
    $this->assertFileExists($this->destURI . '/item1.txt');
    $this->assertFileExists($this->destURI . '/item2.txt');
    $this->assertFileExists($this->destURI . '/item3.txt');
    $this->assertFileExists($this->destURI . '/item4.txt');
    $this->assertEquals('Hello, World 4!', file_get_contents($this->destURI . '/item4.txt'));
  }

  /**
   * @covers ::saveFile
   */
  public function testMetadata() {
    // Create with an extension.
    $file = $this->manager->create('txt');
    $file->write('Hello, World 4!');
    $file->setName('item4');
    $file->setMeta('x-example', '12345');

    $this->destination->saveFile($file);

    $this->assertFileExists($this->destURI . '/item4.txt');
    $this->assertEquals('Hello, World 4!', file_get_contents($this->destURI . '/item4.txt'));

    // Dipping beneath the API to test that the sidecar is created
    $this->assertFileExists($this->destURI . '/item4.txt.info');

    // Load the file again and get the metadata
    $file = $this->destination->getFile('item4.txt');
    $file = $this->destination->loadFileMetadata($file);
    $this->assertEquals('12345', $file->getMeta('x-example'));

    // Dipping beneath the API to test that the info file doesn't exist after a delete
    $this->destination->deleteFile('item4.txt');
    $this->assertFileNotExists($this->destURI . '/item4.txt');
    $this->assertFileNotExists($this->destURI . '/item4.txt.info');
  }

}
