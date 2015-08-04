<?php
use BackupMigrate\Core\Config\Config;
use BackupMigrate\Core\Source\FileDirectorySource;
use BackupMigrate\Core\Tests\File\TempFileConsumerTestTrait;

/**
 * @file
 */

class FileDirectorySourceTest extends PHPUnit_Framework_TestCase {
  use TempFileConsumerTestTrait;

  /**
   * @var string A URI for a virtual file
   */
  protected $URI;

  /**
   * @var FileDirectorySource
   */
  protected $source;

  /**
   * @var array
   */
  protected $file_list;

  /**
   * {@inheritdoc}
   */
  public function setUp()
  {
    $this->file_list = [
      'item1.txt' => 'Hello, World 1!',
      'item2.txt' => 'Hello, World 2!',
      'item3.txt' => 'Hello, World 3!',
    ];

    $this->_setUpFiles([
      'tmp' => [],
      'files' => $this->file_list
    ]);

    $this->URI = 'vfs://root/files/';

    $this->source = new FileDirectorySource(new Config(['directory' => $this->URI]));
    $this->source->setArchiveWriter(new \BackupMigrate\Core\Service\PearTarArchiveWriter());
    $this->source->setTempFileManager($this->manager);
  }

  /**
   * @covers exportToFile
   */
  public function testBackup() {
    $file = $this->source->exportToFile();

    // Move the file to the tmp directory so we can use command line untar.
    $tmp = tempnam('/tmp', 'bamtest');
    copy($file->realpath(), $tmp);

    // Untar the file and see if all of the files are there.
    foreach ($this->file_list as $file_name => $contents) {
      $output = null;
      $output = exec('tar xfO ' . $tmp . ' ' . $file_name);
      $this->assertEquals($contents, $output);
    }
  }
}
