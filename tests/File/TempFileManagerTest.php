<?php
/**
 * @file
 */

namespace BackupMigrate\Core\Tests\File;

use BackupMigrate\Core\File\TempFileAdapter;
use BackupMigrate\Core\File\TempFileManager;
use BackupMigrate\Core\Tests\File\TempFileConsumerTestTrait;
use BackupMigrate\Core\File\BackupFileWritableInterface;
use org\bovigo\vfs\vfsStream;


/**
 * @coversDefaultClass \BackupMigrate\Core\Services\TempFileManager
 */
class TempFileManagerTest extends \PHPUnit_Framework_TestCase
{
  use TempFileConsumerTestTrait;

  function setUp() {
    $this->_setUpFiles();
  }


  /**
   * @covers ::__constructor
   */
  public function testCreate() {
    // Create with no extension.
    $file = $this->manager->create();

    // Is this a temp file
    $this->assertInstanceOf('\BackupMigrate\Core\File\BackupFileWritableInterface', $file);

    // Create with an extension.
    $file = $this->manager->create('txt');

    // Is this a temp file
    $this->assertInstanceOf('\BackupMigrate\Core\File\BackupFileWritableInterface', $file);
    $this->assertEquals('txt', $file->getExt());

  }

  /**
   * @covers ::__constructor
   * @covers ::pushExt
   */
  public function testPushExt() {
    // Create with no extension.
    $file = $this->manager->create();
    $this->assertEquals('', $file->getExt());
    // Is this a temp file
    $this->assertInstanceOf('\BackupMigrate\Core\File\BackupFileWritableInterface', $file);
    // Push an extension.
    $file = $this->manager->pushExt($file, 'txt');
    // Is this a temp file
    $this->assertInstanceOf('\BackupMigrate\Core\File\BackupFileWritableInterface', $file);
    // Is the ext correct.
    $this->assertEquals('txt', $file->getExt());
    // Push an extension.
    $file = $this->manager->pushExt($file, 'tar');
    // Is this a temp file
    $this->assertInstanceOf('\BackupMigrate\Core\File\BackupFileWritableInterface', $file);
    // Is the ext correct.
    $this->assertEquals('txt.tar', $file->getExt());
    // Push an extension.
    $file = $this->manager->pushExt($file, 'gz');
    // Is this a temp file
    $this->assertInstanceOf('\BackupMigrate\Core\File\BackupFileWritableInterface', $file);
    // Is the ext correct.
    $this->assertEquals('txt.tar.gz', $file->getExt());

    // Do it again but starting with an extension
    // Create with no extension.
    $file = $this->manager->create('txt');
    // Is this a temp file
    $this->assertInstanceOf('\BackupMigrate\Core\File\BackupFileWritableInterface', $file);
    // Is the ext correct.
    $this->assertEquals('txt', $file->getExt());
    // Push an extension.
    $file = $this->manager->pushExt($file, 'tar');
    // Is this a temp file
    $this->assertInstanceOf('\BackupMigrate\Core\File\BackupFileWritableInterface', $file);
    // Is the ext correct.
    $this->assertEquals('txt.tar', $file->getExt());
    // Push an extension.
    $file = $this->manager->pushExt($file, 'gz');
    // Is this a temp file
    $this->assertInstanceOf('\BackupMigrate\Core\File\BackupFileWritableInterface', $file);
    // Is the ext correct.
    $this->assertEquals('txt.tar.gz', $file->getExt());

  }

  /**
   * @covers ::__constructor
   * @covers ::pushExt
   */
  public function testPopExt() {
    $file = $this->manager->create('txt.tar.gz');
    $this->assertEquals('txt.tar.gz', $file->getExt());

    $file = $this->manager->popExt($file);
    $this->assertInstanceOf('\BackupMigrate\Core\File\BackupFileWritableInterface', $file);
    $this->assertEquals('txt.tar', $file->getExt());
    $file = $this->manager->popExt($file);
    $this->assertInstanceOf('\BackupMigrate\Core\File\BackupFileWritableInterface', $file);
    $this->assertEquals('txt', $file->getExt());
    $file = $this->manager->popExt($file);
    $this->assertInstanceOf('\BackupMigrate\Core\File\BackupFileWritableInterface', $file);
    $this->assertEquals('', $file->getExt());
  }

}