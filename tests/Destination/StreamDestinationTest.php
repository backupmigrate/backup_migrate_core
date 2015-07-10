<?php
use BackupMigrate\Core\Config\Config;
use BackupMigrate\Core\Destination\StreamDestination;
use BackupMigrate\Core\Tests\File\TempFileConsumerTestTrait;

/**
 * @file
 */

class StreamDestinationTest extends PHPUnit_Framework_TestCase {
  use TempFileConsumerTestTrait;

  /**
   * @var StreamDestination
   */
  protected $destination;

  /**
   * @var string
   */
  protected $streamURI;

  /**
   * {@inheritdoc}
   */
  public function setUp()
  {
    $this->_setUpFiles([
      'tmp' => [],
      'files' => []
    ]);

    $this->streamURI = 'vfs://root/files/output.txt';
    $this->destination = new StreamDestination(new Config(['streamuri' => $this->streamURI]));
  }

  /**
   * @covers ::countFiles
   */
  public function testCountFiles() {
    $files = $this->destination->countFiles();
    $this->assertEquals(0, $files);
  }

  /**
   * @covers ::listFiles
   */
  public function testListFiles() {
    $files = $this->destination->listFiles();
    $this->assertEmpty($files);
  }

  /**
   * @covers ::loadFileForReading
   */
  public function testLoad() {
    $file = $this->destination->getFile('item1.txt');
    $this->assertNull($file);
//    $file = $this->destination->loadFileForReading($file);
//    $this->assertNull($file);
  }

  /**
   * @covers ::deleteFile
   */
  public function testDelete() {
    $this->destination->deleteFile('item1.txt');
  }

  /**
   * @covers ::saveFile
   */
  public function testSave() {
    // Create with an extension.
    $file = $this->manager->create('txt');
    $file->writeAll('Hello, World 4!');
    $file->setName('item4');

    $this->destination->saveFile($file);
    $this->assertEquals('Hello, World 4!', file_get_contents($this->streamURI));
  }

}
