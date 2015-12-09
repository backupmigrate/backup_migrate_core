<?php
/**
 * @file
 */

namespace BackupMigrate\Core\Service;


use BackupMigrate\Core\File\WritableStreamBackupFile;
use BackupMigrate\Core\Tests\File\TempFileConsumerTestTrait;
use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * Class TarArchiveReaderTest
 * @package BackupMigrate\Core\Service
 */
class TarArchiveReaderTest extends \PHPUnit_Framework_TestCase {
  use TempFileConsumerTestTrait;

  /**
   * @var TarArchiveReader
   */
  protected $reader;

  /**
   * @var TarArchiveWriter
   */
  protected $archiver;

  /**
   * @var array
   */
  protected $file_list;

  public function setUp() {
    $this->file_list =  [
      'item1.txt' => 'Hello, World 1!',
      'item2.txt' => 'Hello, World 2!',
      'item3.txt' => 'Hello, World 3!',
      'subdir/subitem1.txt' => 'Hello, World 4!',
    ];

    // Add a file with a very long name
    $name = '';
    for ($i = 0; $i < 10; $i++) {
      $name .= 'abc1234567890';
    }
    $this->file_list[$name] = 'Hello, World 5!';

    $this->_setUpFiles([
      'tmp' => [],
      'output' => [],
      'files' => $this->file_list
    ]);


    $this->reader = new TarArchiveReader();
    $this->archiver = new TarArchiveWriter();
  }

  /**
   * @covers ::getFileExt
   */
  public function testGetFileExt() {
//    $this->assertEquals('tar', $this->reader->getFileExt());
  }

  /**
   * @covers ::setOutput
   * @covers ::addFile
   * @covers ::closeArchive
   */
  public function testUnArchiveFiles() {
    $output_file = tempnam('/tmp', 'test');
    $file = new WritableStreamBackupFile($output_file);
    $this->archiver->setArchive($file);

    $file_names = array_keys($this->file_list);

    foreach ($file_names as $filename) {
      $this->archiver->addFile('vfs://root/files/' . $filename, $filename);
    }
    $this->archiver->closeArchive();

    $output_dir=tempnam(sys_get_temp_dir(),'');
    if (file_exists($output_dir)) {
      unlink($output_dir);
    }
    mkdir($output_dir);

    $this->reader->setArchive($file);
    $this->reader->extractTo($output_dir);

    // Recursively read the output directory.
    $objects = new RecursiveDirectoryIterator($output_dir, FilesystemIterator::SKIP_DOTS);
    $file_list = [];
    foreach (new RecursiveIteratorIterator($objects) as $filename => $cur) {
      $file_list[] = substr($filename, strlen($output_dir) + 1);
    }
    sort($file_names);
    sort($file_list);

    // Make sure the files all exist with the correct names.
    $this->assertEquals($file_names, $file_list);

    foreach ($this->file_list as $file_name => $contents) {
      $output = file_get_contents($output_dir . '/' . $file_name);
      $this->assertEquals($contents, $output);
    }
  }
}
