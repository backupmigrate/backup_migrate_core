<?php
/**
 * @file
 */

namespace BackupMigrate\Core\Tests\Service;
use BackupMigrate\Core\File\ReadableStreamBackupFile;
use BackupMigrate\Core\Service\PearTarArchiveReader;
use BackupMigrate\Core\Tests\File\TempFileConsumerTestTrait;


/**
 * Class PearTarArchiveReaderTest
 * @package BackupMigrate\Core\Tests\Service
 */
class PearTarArchiveReaderTest extends \PHPUnit_Framework_TestCase {
  use TempFileConsumerTestTrait;

  /**
   * @var PearTarArchiveReader
   */
  protected $archiver;

  /**
   * @var string
   */
  protected $input_file;

  /**
   * @var array
   */
  protected $file_list;

  public function setUp() {
    $this->file_list =  [
      'item1.txt' => 'Hello, World 1!',
      'item2.txt' => 'Hello, World 2!',
      'item3.txt' => 'Hello, World 3!',
    ];


    // TODO: Fix all this temp file messiness. Make it less dependent on the OS.
    $input_file = tempnam('/tmp', 'bamtest');
    exec('tar --create --file=' .  $input_file . ' --files-from=/dev/null');
    foreach ($this->file_list as $name => $body) {
      file_put_contents('/tmp/'.$name, $body);
      exec("tar -C /tmp/ --append --file=$input_file $name");
    }
    $this->input_file = $input_file;


    $this->archiver = new PearTarArchiveReader();

    $this->_setUpFiles([
      'out' => [],
    ]);

  }

  /**
   * @covers ::getFileExt
   */
  public function testGetFileExt() {
    $this->assertEquals('tar', $this->archiver->getFileExt());
  }

  /**
   * @covers ::setInput
   * @covers ::extractTo
   */
  public function testReadArchive() {
    $this->archiver->setInput(new ReadableStreamBackupFile($this->input_file));

    $this->archiver->extractTo('vfs://root/out/');

    foreach ($this->file_list as $name => $body) {
      $this->assertNotEmpty(file_exists('vfs://root/out/' . $name));
      $this->assertEquals($body, file_get_contents('vfs://root/out/' . $name));
    }
  }
}
