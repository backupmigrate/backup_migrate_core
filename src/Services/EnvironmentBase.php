<?php
/**
 * @file
 * Contains \BackupMigrate\Core\Services\ApplicationBase.
 */

namespace BackupMigrate\Core\Services;

use BackupMigrate\Core\Util\Mailer;
use BackupMigrate\Core\Util\MailerInterface;
use BackupMigrate\Core\Util\NullCache;
use BackupMigrate\Core\Util\NullState;
use BackupMigrate\Core\Util\StateInterface;
use BackupMigrate\Core\Util\CacheInterface;
use BackupMigrate\Core\Services\TempFileManagerInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * Class EnvironmentBase
 *
 * A basic environment that can have it's components injected.
 *
 * @package BackupMigrate\Core\Services
 */
class EnvironmentBase implements EnvironmentInterface {

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
   * @var \Psr\Log\LoggerInterface
   */
  protected $logger;

  /**
   * @var \BackupMigrate\Core\Util\MailerInterface
   */
  protected $mailer;



  /**
   * @param \BackupMigrate\Core\Services\TempFileManagerInterface $tempFileManager
   * @param \BackupMigrate\Core\Util\CacheInterface $cacheManager
   * @param \BackupMigrate\Core\Util\StateInterface $stateManager
   * @param \Psr\Log\LoggerInterface $logger
   * @param \BackupMigrate\Core\Util\MailerInterface $mailer
   */
  public function __construct(TempFileManagerInterface $tempFileManager = NULL, CacheInterface $cacheManager = NULL, StateInterface $stateManager = NULL, LoggerInterface $logger = NULL, MailerInterface $mailer = NULL) {
    $this->tempFileManager = $tempFileManager ? $tempFileManager : new TempFileManager(new TempFileAdapter('/tmp'));
    $this->cacheManager = $cacheManager ? $cacheManager : new NullCache();
    $this->stateManager = $stateManager ? $stateManager : new NullState();
    $this->logger = $logger ? $logger : new NullLogger();
    $this->mailer = $mailer ? $mailer : new Mailer();
  }

  /**
   * @return \BackupMigrate\Core\Services\TempFileManagerInterface;
   */
  public function getTempFileManager() {
    return $this->tempFileManager;
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

  /**
   * Get the full ID string for the application.
   *
   * @return string
   */
  public function getIDString() {
   return $this->getName() . ' v. ' . $this->getVersion() . ' (' . $this->getProjectURL() . ')';
  }

  /**
   * Get the name of the application
   *
   * @return string
   */
  public function getName() {
    return 'Backup and Migrate Core';
  }

  /**
   * Get the version number of the application.
   *
   * @return string
   */
  public function getVersion() {
    return '0.0.1';
  }

  /**
   * Get the version number of the application.
   *
   * @return string
   */
  public function getProjectURL() {
    return 'http://github.com/backupmigrate';
  }
}