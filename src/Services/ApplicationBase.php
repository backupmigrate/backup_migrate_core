<?php
/**
 * @file
 * Contains \BackupMigrate\Core\Services\ApplicationBase.
 */

namespace BackupMigrate\Core\Services;

use BackupMigrate\Core\Services\ApplicationInterface;

use BackupMigrate\Core\Util\NullCache;
use BackupMigrate\Core\Util\NullState;
use BackupMigrate\Core\Util\StateInterface;
use BackupMigrate\Core\Util\CacheInterface;
use BackupMigrate\Core\Services\TempFileManagerInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * Class ApplicationBase
 *
 * A basic application that can have it's components injected.
 *
 * @package BackupMigrate\Core\Services
 */
class ApplicationBase implements ApplicationInterface {

  /**
   * @var \BackupMigrate\Core\Util\CacheInterface;
   */
  protected $cacheManager;

  /**
   * @var \BackupMigrate\Core\Util\StateInterface;
   */
  protected $stateManager;

  /**
   * @var \BackupMigrate\Core\Services\TempFileManagerInterface
   */
  protected $tempFileManager;

  /**
   * @var
   */
  protected $logger;


  /**
   * @param \BackupMigrate\Core\Services\TempFileManagerInterface $tempFileManager
   * @param \BackupMigrate\Core\Util\CacheInterface $cacheManager
   * @param \BackupMigrate\Core\Util\StateInterface $stateManager
   * @param \Psr\Log\LoggerInterface $logger
   */
  public function __construct(TempFileManagerInterface $tempFileManager, CacheInterface $cacheManager = NULL, StateInterface $stateManager = NULL, LoggerInterface $logger) {
    $this->tempFileManager = $tempFileManager;
    $this->cacheManager = $cacheManager ? $cacheManager : new NullCache();
    $this->stateManager = $stateManager ? $stateManager : new NullState();
    $this->logger = $logger ? $logger : new NullLogger();
  }

  /**
   * @return \BackupMigrate\Core\Services\TempFileManagerInterface;
   */
  public function getTempFileManager() {
    return $this->$tempFileManager;
  }

  /**
   * @return \BackupMigrate\Core\Util\CacheInterface;
   */
  public function getCacheManager() {
    return $this->cacheManager;
  }

  /**
   * @return \BackupMigrate\Core\Util\StateInterface;
   */
  public function getStateManager() {
    return $this->stateManager;
  }


  /**
   * @return \BackupMigrate\Core\Services\TempFileManagerInterface;
   */
  public function getLogger() {
    return $this->logger;
  }
}