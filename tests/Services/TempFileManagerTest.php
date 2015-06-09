<?php
/**
 * @file
 */

namespace BackupMigrate\Core\Tests\Services;

use \BackupMigrate\Core\Services\TempFileAdapter;
use \BackupMigrate\Core\Services\TempFileManager;
use \BackupMigrate\Core\Util\BackupFileWritableInterface;


/**
 * @coversDefaultClass \BackupMigrate\Core\Services\TempFileManager
 */
class TempFileManagerTest extends \PHPUnit_Framework_TestCase
{

  /**
   * @var \BackupMigrate\Core\Services\TempFileAdapter
   */
  protected $adapter;

  /**
   * @var \BackupMigrate\Core\Services\TempFileAdapter
   */
  protected $manager;

  /**
   * {@inheritdoc}
   */
  public function setUp()
  {
    $this->adapter = new TempFileAdapter('/tmp/', 'abc');
    $this->manager = new TempFileManager($this->adapter);
  }

  /**
   * @covers ::__constructor
   */
  public function testCreate() {
    // Create with no extension.
    $file = $this->manager->create();

    // Is this a temp file
    $this->assertInstanceOf('\BackupMigrate\Core\Util\BackupFileWritableInterface', $file);

    // Create with an extension.
    $file = $this->manager->create('txt');

    // Is this a temp file
    $this->assertInstanceOf('\BackupMigrate\Core\Util\BackupFileWritableInterface', $file);
    $this->assertEquals('txt', $file->getMeta('ext'));

  }

  /**
   * @covers ::__constructor
   * @covers ::pushExt
   */
  public function testPushExt() {
    // Create with no extension.
    $file = $this->manager->create();
    $this->assertEquals('', $file->getMeta('ext'));
    // Is this a temp file
    $this->assertInstanceOf('\BackupMigrate\Core\Util\BackupFileWritableInterface', $file);
    // Push an extension.
    $file = $this->manager->pushExt($file, 'txt');
    // Is this a temp file
    $this->assertInstanceOf('\BackupMigrate\Core\Util\BackupFileWritableInterface', $file);
    // Is the ext correct.
    $this->assertEquals('txt', $file->getMeta('ext'));
    // Push an extension.
    $file = $this->manager->pushExt($file, 'tar');
    // Is this a temp file
    $this->assertInstanceOf('\BackupMigrate\Core\Util\BackupFileWritableInterface', $file);
    // Is the ext correct.
    $this->assertEquals('txt.tar', $file->getMeta('ext'));
    // Push an extension.
    $file = $this->manager->pushExt($file, 'gz');
    // Is this a temp file
    $this->assertInstanceOf('\BackupMigrate\Core\Util\BackupFileWritableInterface', $file);
    // Is the ext correct.
    $this->assertEquals('txt.tar.gz', $file->getMeta('ext'));

    // Do it again but starting with an extension
    // Create with no extension.
    $file = $this->manager->create('txt');
    // Is this a temp file
    $this->assertInstanceOf('\BackupMigrate\Core\Util\BackupFileWritableInterface', $file);
    // Is the ext correct.
    $this->assertEquals('txt', $file->getMeta('ext'));
    // Push an extension.
    $file = $this->manager->pushExt($file, 'tar');
    // Is this a temp file
    $this->assertInstanceOf('\BackupMigrate\Core\Util\BackupFileWritableInterface', $file);
    // Is the ext correct.
    $this->assertEquals('txt.tar', $file->getMeta('ext'));
    // Push an extension.
    $file = $this->manager->pushExt($file, 'gz');
    // Is this a temp file
    $this->assertInstanceOf('\BackupMigrate\Core\Util\BackupFileWritableInterface', $file);
    // Is the ext correct.
    $this->assertEquals('txt.tar.gz', $file->getMeta('ext'));

  }

  /**
   * @covers ::__constructor
   * @covers ::pushExt
   */
  public function testPopExt() {
    $file = $this->manager->create('txt.tar.gz');
    $this->assertEquals('txt.tar.gz', $file->getMeta('ext'));

    $file = $this->manager->popExt($file);
    $this->assertInstanceOf('\BackupMigrate\Core\Util\BackupFileWritableInterface', $file);
    $this->assertEquals('txt.tar', $file->getMeta('ext'));
    $file = $this->manager->popExt($file);
    $this->assertInstanceOf('\BackupMigrate\Core\Util\BackupFileWritableInterface', $file);
    $this->assertEquals('txt', $file->getMeta('ext'));
    $file = $this->manager->popExt($file);
    $this->assertInstanceOf('\BackupMigrate\Core\Util\BackupFileWritableInterface', $file);
    $this->assertEquals('', $file->getMeta('ext'));
  }

}