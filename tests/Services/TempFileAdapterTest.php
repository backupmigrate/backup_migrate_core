<?php
namespace BackupMigrate\Core\Tests\Services;

use \BackupMigrate\Core\Services\TempFileAdapter;

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
     * {@inheritdoc}
     */
    public function setUp()
    {
      $this->adapter = new TempFileAdapter('/tmp', 'abc');
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
      $new_adapter = new TempFileAdapter('/tmp', 'bca');
      $path = $new_adapter->createTempFile();
      $this->assertStringStartsWith('bca', basename($path));

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
