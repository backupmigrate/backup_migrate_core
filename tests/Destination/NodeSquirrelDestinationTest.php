<?php
use BackupMigrate\Core\Destination\NodeSquirrelDestination;
use BackupMigrate\Core\File\BackupFileInterface;
use BackupMigrate\Core\Service\NodeSquirrelClient;
use BackupMigrate\Core\Tests\File\TempFileConsumerTestTrait;


/**
 * Class NodeSquirrelDestinationTest
 */
class NodeSquirrelDestinationTest extends PHPUnit_Framework_TestCase {
  use TempFileConsumerTestTrait;

  protected $nodesquirrel_client;
  protected $destination;

  public function setUp() {
    parent::setUp();

    $this->_setUpFiles([
      'tmp' => [],
    ]);


    $this->nodesquirrel_client = $this->getMock(NodeSquirrelClient::class);
    $this->destination = new NodeSquirrelDestination();
    $this->destination->setNodeSquirrelClient($this->nodesquirrel_client);
  }


  public function testListFiles() {
    $api_result = [
      'item1.txt' => ['filesize' => 123, 'timestamp' => 1489959884, 'filename' => 'item1.txt'],
      'item2.txt' => ['filesize' => 123, 'timestamp' => 1489959883, 'filename' => 'item2.txt'],
      'item3.txt' => ['filesize' => 123, 'timestamp' => 1489959882, 'filename' => 'item3.txt'],
    ];

    $this->nodesquirrel_client->expects($this->once())
      ->method('listFiles')
      ->willReturn($api_result);

    $files = $this->destination->listFiles();
    $this->assertArrayHasKey('item1.txt', $files);
    $this->assertArrayHasKey('item2.txt', $files);
    $this->assertArrayHasKey('item3.txt', $files);

    foreach ($files as $key => $file) {
      $this->assertInstanceOf(BackupFileInterface::class, $file);
      $this->assertEquals($api_result[$key]['timestamp'], $file->getMeta('timestamp'));
      $this->assertEquals($api_result[$key]['filesize'], $file->getMeta('filesize'));
    }
  }

  public function testGetFile() {
    $api_result = [
      'item1.txt' => ['filesize' => 123, 'timestamp' => 1489959884, 'filename' => 'item1.txt'],
      'item2.txt' => ['filesize' => 123, 'timestamp' => 1489959883, 'filename' => 'item2.txt'],
      'item3.txt' => ['filesize' => 123, 'timestamp' => 1489959882, 'filename' => 'item3.txt'],
    ];

    $this->nodesquirrel_client->expects($this->any())
      ->method('listFiles')
      ->willReturn($api_result);

    foreach ($api_result as $key => $info) {
      $file = $this->destination->getFile($key);

      $this->assertInstanceOf(BackupFileInterface::class, $file);
      $this->assertEquals($info['timestamp'], $file->getMeta('timestamp'));
      $this->assertEquals($info['filesize'], $file->getMeta('filesize'));
    }
  }
  
  public function testSaveFile() {
    // Create with an extension.
    $file = $this->manager->create('txt');
    $file->write('Hello, World 4!');
    $file->setName('item4');

    $this->nodesquirrel_client->expects($this->once())
      ->method('uploadFile')
      ->with($file);

    $this->destination->saveFile($file);
  }


}
