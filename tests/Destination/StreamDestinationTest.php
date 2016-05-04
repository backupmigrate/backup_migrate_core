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
