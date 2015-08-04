<?php
/**
 * @file
 */

use BackupMigrate\Core\Config\Config;
use BackupMigrate\Core\Service\EnvironmentBase;
use \BackupMigrate\Core\Filter\CompressionFilter;
use BackupMigrate\Core\File\TempFileAdapter;
use BackupMigrate\Core\File\TempFileManager;
use BackupMigrate\Core\File\ReadableStreamBackupFile;
use BackupMigrate\Core\Plugin\PluginManager;
use org\bovigo\vfs\vfsStream;


/**
 * Class CompressionFilterTest
 */
class CompressionFilterTest extends PHPUnit_Framework_TestCase {

  /**
   * @var TempFileManager
   */
  protected $manager;

  /**
   * @var vfsStream
   */
  protected $root;

  protected $filedir;

  /**
   * @var CompressionFilter
   */
  protected $filter;

  protected $original;
  protected $gzipped;

  /**
   * {@inheritdoc}
   */
  public function setUp()
  {

    $this->original = 'Hello, World!';

    // @TODO vfsStream does not appear to work with gzopen. see: https://github.com/mikey179/vfsStream/issues/3
    $this->filedir = '/tmp/bmtest';
    $this->cleanUpFiles($this->filedir);
    mkdir($this->filedir);
    file_put_contents($this->filedir . '/item1.txt', $this->original);
    file_put_contents($this->filedir . '/item2.txt.gz', gzencode($this->original));
    file_put_contents($this->filedir . '/item3.txt.bz2', bzcompress($this->original));

    // Create a tmp manager.
    $this->manager = new TempFileManager(new TempFileAdapter('/tmp'));

    $this->filter = new CompressionFilter(
      ['compression' => 'gzip']
    );
    $this->filter->setTempFileManager($this->manager);
  }

  public function tearDown() {
    unset($this->manager);

    $this->cleanUpFiles($this->filedir);
  }

  /**
   * @param $dir
   */
  protected function cleanUpFiles($dir) {
    // Remove our file directory.
    if (file_exists($dir)) {
      foreach (array_diff(scandir($dir), array('..', '.')) as $file) {
        unlink($dir . '/' . $file);
      }
      rmdir($dir);
    }
  }

  /**
   * @covers ::afterBackup
   */
  public function testCompress() {
    $file = new ReadableStreamBackupFile($this->filedir . '/item1.txt');

    // Gzip
    $compressed = $this->filter->afterBackup($file);
    $this->assertEquals('item1.txt.gz', $compressed->getFullName());
    $this->assertNotEquals($this->original, $compressed->readAll());
    $this->assertEquals($this->original, gzdecode($compressed->readAll()));
    $this->assertEquals($this->original, $file->readAll());

    // No compression
    $this->filter->setConfig(new Config(['compression' => 'none']));
    $compressed = $this->filter->afterBackup($file);
    $this->assertEquals('item1.txt', $compressed->getFullName());
    $this->assertEquals($this->original, $compressed->readAll());
    $this->assertEquals($this->original, $file->readAll());

    // Bzip
    $this->filter->setConfig(new Config(['compression' => 'bzip']));
    $compressed = $this->filter->afterBackup($file);
    $this->assertEquals('item1.txt.bz2', $compressed->getFullName());
    $handle = bzopen($compressed->realpath(), 'r');
    $this->assertEquals($this->original, bzread($handle));
    $this->assertNotEquals($this->original, $compressed->readAll());
    $this->assertEquals($this->original, $file->readAll());


    // Zip
//    $this->filter->setConfig(new Config(['compression' => 'zip']));
//    $compressed = $this->filter->afterBackup($file);
//    $this->assertEquals('item1.txt.zip', $compressed->getFullName());
//    $this->assertNotEquals($this->original, $compressed->readAll());
//    $this->assertEquals($this->original, $file->readAll());
//
//    $handle = zip_open($compressed->realpath());
//    $file_handle = zip_read (zip_open($compressed->realpath()));
//    $this->assertEquals($this->original, bzread($handle));

  }

  /**
   * @covers ::beforeRestore
   */
  public function testDecompress() {

    $file = new ReadableStreamBackupFile($this->filedir .   '/item2.txt.gz');
    $decompressed = $this->filter->beforeRestore($file);
    $this->assertEquals('item2.txt', $decompressed->getFullName());
    $this->assertEquals($this->original, $decompressed->readAll());
    $this->assertEquals(file_get_contents($this->filedir . '/item2.txt.gz'), $file->readAll());


    $file = new ReadableStreamBackupFile($this->filedir .   '/item3.txt.bz2');
    $decompressed = $this->filter->beforeRestore($file);
    $this->assertEquals('item3.txt', $decompressed->getFullName());
    $this->assertEquals($this->original, $decompressed->readAll());
    $this->assertEquals(file_get_contents($this->filedir . '/item3.txt.bz2'), $file->readAll());
    $file = new ReadableStreamBackupFile($this->filedir .   '/item3.txt.bz2');


    $file = new ReadableStreamBackupFile($this->filedir .   '/item1.txt');
    $decompressed = $this->filter->beforeRestore($file);
    $this->assertEquals('item1.txt', $decompressed->getFullName());
    $this->assertEquals($this->original, $decompressed->readAll());
    $this->assertEquals(file_get_contents($this->filedir . '/item1.txt'), $file->readAll());

  }

  /**
   * @covers ::beforeRestore
   * @covers ::afterBackup
   */
  public function testRoundTrip() {

    $file = new ReadableStreamBackupFile($this->filedir . '/item1.txt');

    $this->filter->setConfig(new Config(['compression' => 'bzip']));
    $compressed = $this->filter->afterBackup($file);
    $decompressed = $this->filter->beforeRestore($compressed);
    $this->assertEquals($this->original, $decompressed->readAll());
    $this->assertNotEquals($this->original, $compressed->readAll());
    $this->assertEquals($this->original, $file->readAll());

    $this->filter->setConfig(new Config(['compression' => 'gzip']));
    $compressed = $this->filter->afterBackup($file);
    $decompressed = $this->filter->beforeRestore($compressed);
    $this->assertEquals($this->original, $decompressed->readAll());
    $this->assertNotEquals($this->original, $compressed->readAll());
    $this->assertEquals($this->original, $file->readAll());

    $this->filter->setConfig(new Config(['compression' => 'none']));
    $compressed = $this->filter->afterBackup($file);
    $decompressed = $this->filter->beforeRestore($compressed);
    $this->assertEquals($this->original, $decompressed->readAll());
    $this->assertEquals($this->original, $compressed->readAll());
    $this->assertEquals($this->original, $file->readAll());
  }

}
