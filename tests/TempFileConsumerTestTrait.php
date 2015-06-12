<?php
/**
 * @file
 */

namespace BackupMigrate\Core\Tests;

use BackupMigrate\Core\Services\TempFileAdapter;
use BackupMigrate\Core\Services\TempFileManager;
use org\bovigo\vfs\vfsStream;


/**
 * Class TempFileManagerTestTrait
 */
trait TempFileConsumerTestTrait {
  /**
   * @var \BackupMigrate\Core\Services\TempFileAdapter
   */
  protected $adapter;

  /**
   * @var \BackupMigrate\Core\Services\TempFileManager
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
