<?php
namespace BackupMigrate\Core\Tests\Services;

use \BackupMigrate\Core\Services\TempFileManager;

/**
 * @coversDefaultClass \BackupMigrate\Core\Services\TempFileManagerTest
 */
class TempFileManagerTest extends \PHPUnit_Framework_TestCase
{

   /**
     * @var string A URI for a virtual file
     */
    protected $manager;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
      $this->manager = new TempFileManager('/tmp', 'abc');
    }

    /**
     * Create multiple files for testing.
     */
    private function createMultipleFiles($number = 5) {
      $out = array();
      for ($i = 0; $i < $number; $i++) {
        $out[] = $this->manager->createTempFile();
      }
      return $out;
    }

    /**
     * @covers ::__constructor
     * @covers ::createTempFile
     */
    public function testPrefix()
    {
      $path = $this->manager->createTempFile();
      $this->assertStringStartsWith('abc', basename($path));

      // Test another to be sure
      $new_manager = new TempFileManager('/tmp', 'bca');
      $path = $new_manager->createTempFile();
      $this->assertStringStartsWith('bca', basename($path));

    }

    /**
     * @covers ::createTestFile
     * @covers ::deleteTestFile
     */
    public function testCreateDestroyTempFile()
    {
      $path = $this->manager->createTempFile();

      // Make sure a temp file has been created somewhere.
      $this->assertNotEmpty(file_exists($path));
      $this->assertNotEmpty(is_writable($path));

      $this->manager->deleteTempFile($path);
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
      $this->manager->deleteAllTempFiles();

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
      unset($this->manager);
      foreach ($files as $path) {
        $this->assertEmpty(file_exists($path));
      }      
    }

}
