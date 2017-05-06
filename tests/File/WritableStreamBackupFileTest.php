<?php
namespace BackupMigrate\Core\Tests\File;

use BackupMigrate\Core\File\WritableStreamBackupFile;
use BackupMigrate\Core\File\TempFileAdapter;
use BackupMigrate\Core\Tests\File\BackupFileTest;

/**
 * @coversDefaultClass \BackupMigrate\Core\Util\TempFile
 */
class TempFileTest extends \PHPUnit_Framework_TestCase
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
      $this->adapter = new TempFileAdapter('/tmp/', 'abc');
    }

    /**
     * @covers ::__constructor
     * @covers ::__destructor
     * @covers ::realpath
     */
    public function testCreateAndDestroy()
    {
        $file = new WritableStreamBackupFile($this->adapter->createTempFile());
        // Make sure a temp file has been created somewhere.
        $this->assertNotEmpty(file_exists($file->realpath()));
        $this->assertNotEmpty(is_writable($file->realpath()));

        // Destroy the object
      // Removed. Temp files don't manage their own desctuction. That's the adapter's problem
//        $path = $file->realpath();
//        unset($file);
//        $this->assertEmpty(file_exists($path));
    }

    /**
     * @covers ::open
     * @covers ::isOpen
     * @covers ::write
     * @covers ::close
     */
    public function testOpenForWrite()
    {
      $file = new WritableStreamBackupFile($this->adapter->createTempFile());

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
      $new_file = new WritableStreamBackupFile($this->adapter->createTempFile());
      $path = $new_file->realpath();
      $new_file->write('Hello, World!');
      $this->assertEquals(file_get_contents($new_file->realpath()), 'Hello, World!');
      $new_file->close();

      // Check the file size
      $this->assertEquals($new_file->getMeta('filesize'), strlen('Hello, World!'));
//      unset($new_file);
//      // Make sure the file was deleted
//      $this->assertFalse(file_exists($path));
    }

}
