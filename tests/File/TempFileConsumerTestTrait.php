<?php
/**
 * @file
 */

namespace BackupMigrate\Core\Tests\File;

use BackupMigrate\Core\File\TempFileAdapter;
use BackupMigrate\Core\File\TempFileManager;
use org\bovigo\vfs\vfsStream;


/**
 * Class TempFileManagerTestTrait
 */
trait TempFileConsumerTestTrait {
  /**
   * @var \BackupMigrate\Core\File\TempFileAdapter
   */
  protected $adapter;

  /**
   * @var \BackupMigrate\Core\File\TempFileManager
   */
  protected $manager;

  /**
   * @var vfsStream
   */
  protected $root;

  /**
   * {@inheritdoc}
   */
  public function _setUpFiles($structure = NULL)
  {
    $structure = $structure ? : ['tmp' => []];

    $this->root = vfsStream::setup('root', 0777, $structure);
    $this->adapter = new TempFileAdapter($this->root->url() . '/tmp/', 'bmtest_');
    $this->manager = new TempFileManager($this->adapter);
  }
}
