<?php
namespace BackupMigrate\Core\Tests\File;

use BackupMigrate\Core\File\ReadableStreamBackupFile;
use org\bovigo\vfs\vfsStream;

/**
 * @coversDefaultClass \BackupMigrate\Core\Util\ReadableStreamBackupFile
 */
class ReadableStreamBackupFileTest extends \PHPUnit_Framework_TestCase
{
    
    /**
     * @var \BackupMigrate\Core\File\BackupFile
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

      $this->file = new ReadableStreamBackupFile($this->fileURI);
    }

    /**
     * @covers ::__constructor
     * @covers ::__destructor
     * @covers ::realpath
     */
    public function testSetupAndDestroy()
    {
      $file = new ReadableStreamBackupFile($this->fileURI);

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
      $handle = $this->file->openForRead();

      // Read a limited number of bytes
      $this->assertEquals($this->file->readBytes(5), 'Hello');
      // Skip ', '
      $this->file->readBytes(2);
      $this->assertEquals($this->file->readBytes(6), 'World!');

      $this->assertEmpty($this->file->readLine());
      $this->assertEmpty($this->file->readBytes(100));

      // Reset the file handle
      $this->file->rewind();
      // Read the entire file
      $this->assertEquals($this->file->readLine(), 'Hello, World!');

      // Read all resets automatically
      $this->assertEquals($this->file->readAll(), 'Hello, World!');
      $this->assertEquals($this->file->readAll(), 'Hello, World!');


      // Close the file again.
      $this->file->close();
      $this->assertFalse($this->file->isOpen());
      $this->assertFalse(is_resource($handle));

      // Test implicit file open and close.
      $new_file = new ReadableStreamBackupFile($this->fileURI);
      $this->assertEquals('Hello, World!', $new_file->readAll());
      unset($new_file);
      // Not sure how to test that the handle has been
      // closed since we don't get direct access to it.
      // Multiline file read:
      $message = "First Line\nSecond Line";
      vfsStream::create(['multiline.txt' => "First Line\nSecond Line"]);

      $file = new ReadableStreamBackupFile('vfs://dir/multiline.txt');
      $this->assertEquals($message, $file->readAll());

      $file->rewind();
      $this->assertEquals('First Line', trim($file->readLine()));
      $this->assertEquals('Second Line', $file->readLine());


    }
}
