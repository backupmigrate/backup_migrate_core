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
        'item2.dat' => 'Hello, World 2!',
        'abc.txt'   => 'Hello, World 3!',
        // TODO: Test subdirectories
        'subdir' => [
          'item4.txt' => 'Hello, World 4!',
          'item5.dat' => 'Hello, World 5!'
        ]
      ]
    ];

    $this->_setUpFiles($this->file_list);

    $this->URI = 'vfs://root/files/';

    $this->source = $this->newSource(['directory' => $this->URI]);
  }

  public function tearDown() {
    $this->_tearDownFiles();
  }

  /**
   * @param array $config
   * @return \BackupMigrate\Core\Source\FileDirectorySource
   */
  private function newSource($config = array()) {
    $source = new FileDirectorySource(new Config($config));
    $source->setArchiveReader(new \BackupMigrate\Core\Service\TarArchiveReader());
    $source->setArchiveWriter(new \BackupMigrate\Core\Service\TarArchiveWriter());
    $source->setTempFileManager($this->manager);
    return $source;
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
    $source->setArchiveWriter(new \BackupMigrate\Core\Service\TarArchiveWriter());
    $source->setArchiveReader(new \BackupMigrate\Core\Service\TarArchiveReader());
    $source->setTempFileManager($this->manager);
    $source->importFromFile($file);


    $result = vfsStream::inspect(new vfsStreamStructureVisitor())->getStructure();
    $this->assertEquals(
      $this->file_list['files'],
      $result['root']['restore']
    );

    // Clean up
    unlink($tarball);
  }


  /**
   * Export a tarball from a source and match it's contents to the expected file list.
   * @param $source
   * @param $files
   * @throws \BackupMigrate\Core\Exception\BackupMigrateException
   */
  private function _exportAndTest($source, $files) {
    $file = $source->exportToFile();

    // Move the file to the tmp directory so we can use command line untar.
    $tarball = tempnam('/tmp', 'bamtest');
    copy($file->realpath(), $tarball);

    // Untar the file and see if all of the files are there.
    $this->_compareTarballToFilelist($files, $tarball);

    // Clean up
    unlink($tarball);
  }


  /**
   * Recursively check an entire directory against a tarball.
   *
   * @param $files
   * @param $tarball
   * @param string $base_dir
   */
  private function _compareTarballToFilelist($files, $tarball, $base_dir = '') {
    $actual = array();
    exec('tar tf ' . $tarball, $actual);

    $expected = $this->_flattenFileList($files);

    // Sort and dedupe lists
    $actual = array_unique($actual);
    sort($actual);

    $expected = array_unique($expected);
    sort($expected);

    $this->assertEquals($expected, $actual);


    // Make sure the file contents all match.
    $this->_compareTarballFileContents($files, $tarball);
  }

  /**
   * Recursively check an entire directory against a tarball.
   *
   * @param $files
   * @param $tarball
   * @param string $base_dir
   */
  private function _compareTarballFileContents($files, $tarball, $base_dir = '') {
    foreach ($files as $file_name => $contents) {
      if (is_array($contents)) {
        $this->_compareTarballFileContents($contents, $tarball, $file_name . '/');
      }
      else {
        $output = exec('tar xfO ' . $tarball . ' ' . $base_dir . $file_name);
        $this->assertEquals($contents, $output);
      }
    }
  }

  /**
   * @param $files
   * @param string $base_dir
   * @return array
   */
  private function _flattenFileList($files, $base_dir = '') {
    $out = array();
    foreach ($files as $file_name => $contents) {
      if (is_array($contents)) {
        $out = array_merge($out, $this->_flattenFileList($contents, $file_name . '/'));
      }
      else {
        $out[] = $base_dir . $file_name;
      }
    }
    return $out;
  }
}
