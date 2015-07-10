<?php
namespace BackupMigrate\Core\Tests\File;

use BackupMigrate\Core\File\TempFileAdapter;
use org\bovigo\vfs\vfsStream;

/**
 * @coversDefaultClass \BackupMigrate\Core\Services\TempFileManager
 */
class TempFileAdapterTest extends \PHPUnit_Framework_TestCase
{

   /**
     * @var string A URI for a virtual file
     */
    protected $adapter;

    /**
    * @var vfsStream
    */
    protected $root;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
      $this->root = vfsStream::setup('root', 0777, ['tmp' => []]);
      $this->adapter = new TempFileAdapter($this->root->url() . '/tmp/', 'abc');
    }

    /**
     * Create multiple files for testing.
     */
    private function createMultipleFiles($number = 5) {
      $out = array();
      for ($i = 0; $i < $number; $i++) {
        $out[] = $this->adapter->createTempFile();
      }
      return $out;
    }

    /**
     * @covers ::__constructor
     * @covers ::createTempFile
     */
    public function testPrefix()
    {
      $path = $this->adapter->createTempFile();
      $this->assertStringStartsWith('abc', basename($path));

      // Test another to be sure
      $new_adapter = new TempFileAdapter('/tmp/', 'bca');
      $path = $new_adapter->createTempFile();
      $this->assertStringStartsWith('bca', basename($path));

    }

  /**
   * @covers ::__constructor
   * @covers ::createTempFile
   */
  public function testExt()
  {
    $path = $this->adapter->createTempFile('txt');
    $this->assertStringEndsWith('.txt', basename($path));

    // Test another to be sure
    $path = $this->adapter->createTempFile('dat');
    $this->assertStringEndsWith('.dat', basename($path));

  }

    /**
     * @covers ::createTestFile
     * @covers ::deleteTestFile
     */
    public function testCreateDestroyTempFile()
    {
      $path = $this->adapter->createTempFile();

      // Make sure a temp file has been created somewhere.
      $this->assertNotEmpty(file_exists($path));
      $this->assertNotEmpty(is_writable($path));

      $this->adapter->deleteTempFile($path);
      $this->assertEmpty(file_exists($path));
    }

    /**
     * @covers ::createTempFile
     * @covers ::deleteAllTempFiles
     */
    public function testDeleteAllTempFiles()
    {
      $files = $this->createMultipleFiles();
      foreach ($files as $path) {
        $this->assertNotEmpty(file_exists($path));
      }
      $this->adapter->deleteAllTempFiles();

      foreach ($files as $path) {
        $this->assertEmpty(file_exists($path));
      }
    }

    /**
     * @covers ::__destructor
     */
    public function testCleanUp() {
      $files = $this->createMultipleFiles();
      foreach ($files as $path) {
        $this->assertNotEmpty(file_exists($path));
      }
      unset($this->adapter);
      foreach ($files as $path) {
        $this->assertEmpty(file_exists($path));
      }      
    }

}
