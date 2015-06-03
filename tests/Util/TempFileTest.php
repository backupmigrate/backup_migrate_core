<?php
namespace BackupMigrate\Core\Tests\Util;

use \BackupMigrate\Core\Util\TempFile;
use \BackupMigrate\Core\Services\TempFileManager;
use \BackupMigrate\Core\Tests\Util\BackupFileTest;

/**
 * @coversDefaultClass \BackupMigrate\Core\Util\TempFile
 */
class TempFileTest extends \PHPUnit_Framework_TestCase
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
     * @covers ::__constructor
     * @covers ::__destructor
     * @covers ::realpath
     */
    public function testCreateAndDestroy()
    {
        $file = new TempFile($this->manager);
        // Make sure a temp file has been created somewhere.
        $this->assertNotEmpty(file_exists($file->realpath()));
        $this->assertNotEmpty(is_writable($file->realpath()));

        // Destroy the object
        $path = $file->realpath();
        unset($file);
        $this->assertEmpty(file_exists($path));
    }

    /**
     * @covers ::open
     * @covers ::isOpen
     * @covers ::write
     * @covers ::close
     */
    public function testOpenForWrite()
    {
      $file = new TempFile($this->manager);

      // Not open yet
      $this->assertFalse($file->isOpen());

      // Open for reading.
      $handle = $file->openForWrite();

      // Write to the file
      $file->write('Hello');

      $path = $file->realpath();
      $this->assertEquals(file_get_contents($file->realpath()), 'Hello');

      // Append to the file
      $file->write(', World!');

      $this->assertEquals(file_get_contents($file->realpath()), 'Hello, World!');

      $file->close();
      $this->assertFalse($file->isOpen());
      $this->assertFalse(is_resource($handle));

      // Test implicit file open and close.
      $new_file = new TempFile($this->manager);
      $path = $new_file->realpath();
      $new_file->write('Hello, World!');
      $this->assertEquals(file_get_contents($new_file->realpath()), 'Hello, World!');
      unset($new_file);
      // Make sure the file was deleted
      $this->assertFalse(file_exists($path));
    }

}
