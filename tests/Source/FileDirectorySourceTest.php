<?php
use BackupMigrate\Core\Config\Config;
use BackupMigrate\Core\Source\FileDirectorySource;
use BackupMigrate\Core\Tests\File\TempFileConsumerTestTrait;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\visitor\vfsStreamStructureVisitor;

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
      'tmp' => [],
      'restore' => [],
      'files' => [
        'item1.txt' => 'Hello, World 1!',
        'item2.txt' => 'Hello, World 2!',
        'item3.txt' => 'Hello, World 3!',
        // TODO: Test subdirectories
        'subdir' => [
          'item4.txt' => 'Hello, World 4!',
          'item5.txt' => 'Hello, World 5!'
        ]
      ]
    ];

    $this->_setUpFiles($this->file_list);

    $this->URI = 'vfs://root/files/';

    $this->source = new FileDirectorySource(new Config(['directory' => $this->URI]));
    $this->source->setArchiver(new \BackupMigrate\Core\Service\PearTarArchiver());
    $this->source->setTempFileManager($this->manager);
  }

  /**
   * @covers exportToFile
   * @covers importFromFile
   */
  public function testBackupRestore() {
    $file = $this->source->exportToFile();

    // Move the file to the tmp directory so we can use command line untar.
    $tarball = tempnam('/tmp', 'bamtest');
    copy($file->realpath(), $tarball);

    // Untar the file and see if all of the files are there.
    $this->_compareTarballToFilelist($this->file_list['files'], $tarball);

    // Restore to another directory.
    $source = new FileDirectorySource(new Config(['directory' => 'vfs://root/restore/']));
    $source->setArchiver(new \BackupMigrate\Core\Service\PearTarArchiver());
    $source->setTempFileManager($this->manager);
    $source->importFromFile($file);


    $result = vfsStream::inspect(new vfsStreamStructureVisitor())->getStructure();
    $this->assertEquals(
      $this->file_list['files'],
      $result['root']['restore']
    );
  }

  /**
   * Recursively check an entire directory against a tarball.
   *
   * @param $files
   * @param $tarball
   * @param string $base_dir
   */
  private function _compareTarballToFilelist($files, $tarball, $base_dir = '') {
    foreach ($files as $file_name => $contents) {
      if (is_array($contents)) {
        $this->_compareTarballToFilelist($contents, $tarball, $file_name . '/');
      }
      else {
        $output = exec('tar xfO ' . $tarball . ' ' . $base_dir . $file_name);
        $this->assertEquals($contents, $output);
      }
    }
  }
}
