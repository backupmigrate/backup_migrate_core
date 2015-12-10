<?php
/**
 * @file
 */

namespace BackupMigrate\Core\Tests\Filter;


use BackupMigrate\Core\Config\Config;
use BackupMigrate\Core\Filter\FileExcludeFilter;
use BackupMigrate\Core\Source\FileDirectorySource;
use BackupMigrate\Core\Tests\File\TempFileConsumerTestTrait;

/**
 * Class FileExcludeFilterTest
 * @package BackupMigrate\Core\Tests\Filter
 */
class FileExcludeFilterTest extends \PHPUnit_Framework_TestCase {

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
   * @var FileExcludeFilter
   */
  protected $filter;

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
        'subdir' => [
          'item4.txt' => 'Hello, World 4!',
          'item5.dat' => 'Hello, World 5!'
        ]
      ]
    ];

    $this->_setUpFiles($this->file_list);
    $this->URI = 'vfs://root/files/';

    $this->source = $this->newSource(['directory' => $this->URI]);
    $this->filter = new FileExcludeFilter(['source' => null]);
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
  public function testExclude() {

    $files = $expected = $this->file_list['files'];
    $this->_testFilter([], $files, $expected);

    $files = $expected = $this->file_list['files'];
    unset($expected['subdir']);
    $this->_testFilter(['subdir'], $files, $expected);

    $files = $expected = $this->file_list['files'];
    unset($expected['item2.dat']);
    unset($expected['subdir']['item5.dat']);
    $this->_testFilter(['*.dat'], $files, $expected);

    $files = $expected = $this->file_list['files'];
    unset($expected['item1.txt']);
    unset($expected['subdir']['item4.txt']);
    $this->_testFilter(['*item[12345].txt'], $files, $expected);

    $files = $expected = $this->file_list['files'];
    unset($expected['item1.txt']);
    unset($expected['subdir']);
    $this->_testFilter(['*item[12345].txt', 'subdir'], $files, $expected);
  }

  /**
   * @param $filter
   * @param $files
   * @param $expected
   */
  private function _testFilter($exclude, $files, $expected) {
    $filter = new FileExcludeFilter(['source' => $this->source, 'exclude_filepaths' => $exclude]);
    $actual = $this->_filterFiles($filter, $files);
    $this->assertEquals($expected, $actual);
  }

  /**
   * @param $filter
   * @param $files
   * @param string $base_path
   */
  private function _filterFiles($filter, $files, $base_path = '') {
    $actual = [];
    foreach ($files as $key => $file) {
      $path = $base_path . $key;
      $path = $filter->beforeFileBackup($path, ['source' => $this->source, 'base_path' => '']);
      if ($path) {
        if (is_array($file)) {
          $actual[$key] = $this->_filterFiles($filter, $file, $path . '/');
        }
        else {
          $actual[$key] = $file;
        }
      }
    }
    return $actual;
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
