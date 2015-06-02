<?php
namespace BackupMigrate\Core\Tests\Util;

use \BackupMigrate\Core\Util\BackupFile;
use org\bovigo\vfs\vfsStream;

/**
 * @coversDefaultClass \BackupMigrate\Core\Util\BackupFile
 */
class BackupFileTest extends \PHPUnit_Framework_TestCase
{
    
    /**
     * @var \BackupMigrate\Core\Util\BackupFile
     */
    protected $file;

    /**
     * @var string A URI for a virtual file
     */
    protected $fileURI;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        vfsStream::setup('dir');
        vfsStream::setup('dir/subdir');
        vfsStream::create(['test.txt' => 'Hello, World!']);
        $this->fileURI = 'vfs://dir/test.txt';

        $this->file = new BackupFile($this->fileURI);
    }

    /**
     * @covers ::__constructor
     * @covers ::__destructor
     * @covers ::realpath
     */
    public function testSetupAndDestroy()
    {
      $file = new BackupFile($this->fileURI);
      
    }

    /**
     * @covers ::open
     * @covers ::isOpen
     * @covers ::read
     * @covers ::close
     */
    public function testOpenForRead()
    {
      // Not open yet
      $this->assertFalse($this->file->isOpen());

      // Open for reading.
      $handle = $this->file->open();

      // Read a limited number of bytes
      $this->assertEquals($this->file->read(5), 'Hello');
      // Skip ', '
      $this->file->read(2);
      $this->assertEquals($this->file->read(5), 'World');

      // Reset the file handle
      $this->file->rewind();
      // Read the entire file
      $this->assertEquals($this->file->read(), 'Hello, World!');

      // Close the file again.
      $this->file->close();
      $this->assertFalse($this->file->isOpen());
      $this->assertFalse(is_resource($handle));

      // Test implicit file open and close.
      $new_file = new BackupFile($this->fileURI);
      $this->assertEquals($new_file->read(), 'Hello, World!');
      unset($new_file);
      // Not sure how to test that the handle has been closed since we don't get direct access to it.
    }
}
