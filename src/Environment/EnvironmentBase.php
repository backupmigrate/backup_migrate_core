<?php
/**
 * @file
 * Contains \BackupMigrate\Core\Services\ApplicationBase.
 */

namespace BackupMigrate\Core\Environment;

use BackupMigrate\Core\Environment\EnvironmentInterface;
use BackupMigrate\Core\File\TempFileAdapter;
use BackupMigrate\Core\File\TempFileAdapterInterface;
use BackupMigrate\Core\File\TempFileManager;
use BackupMigrate\Core\Environment\Mailer;
use BackupMigrate\Core\Environment\MailerInterface;
use BackupMigrate\Core\Environment\NullCache;
use BackupMigrate\Core\Environment\NullState;
use BackupMigrate\Core\Environment\StateInterface;
use BackupMigrate\Core\Environment\CacheInterface;
use BackupMigrate\Core\File\TempFileManagerInterface;
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
   * @var \BackupMigrate\Core\Environment\CacheInterface;
   */
  protected $cache;

  /**
   * @var \BackupMigrate\Core\Environment\StateInterface;
   */
  protected $state;

  /**
   * @var \Psr\Log\LoggerInterface
   */
  protected $logger;

  /**
   * @var \BackupMigrate\Core\Environment\MailerInterface
   */
  protected $mailer;

  /**
   * @var \BackupMigrate\Core\File\$tempFileAdapterInterface
   */
  protected $tempFileAdapter;


  /**
   * @param \BackupMigrate\Core\File\TempFileAdapterInterface $tempFileAdapter
   * @param \BackupMigrate\Core\Environment\CacheInterface $cacheManager
   * @param \BackupMigrate\Core\Environment\StateInterface $stateManager
   * @param \Psr\Log\LoggerInterface $logger
   * @param \BackupMigrate\Core\Environment\MailerInterface $mailer
   */
  public function __construct(TempFileAdapterInterface $tempFileAdapter = NULL, CacheInterface $cacheManager = NULL, StateInterface $stateManager = NULL, LoggerInterface $logger = NULL, MailerInterface $mailer = NULL) {
    $this->tempFileAdapter = $tempFileAdapter? $tempFileAdapter: new TempFileAdapter('/tmp');
    $this->cache = $cacheManager ? $cacheManager : new NullCache();
    $this->state = $stateManager ? $stateManager : new NullState();
    $this->logger = $logger ? $logger : new NullLogger();
    $this->mailer = $mailer ? $mailer : new Mailer();
  }

  /**
   * @return \BackupMigrate\Core\Environment\CacheInterface;
   */
  public function cache() {
    return $this->cache;
  }

  /**
   * @return \BackupMigrate\Core\Environment\StateInterface;
   */
  public function state() {
    return $this->state;
  }

  /**
   * @return \BackupMigrate\Core\File\TempFileManagerInterface;
   */
  public function logger() {
    return $this->logger;
  }

  /**
   * @return \BackupMigrate\Core\Environment\MailerInterface;
   */
  public function mailer() {
    return $this->mailer;
  }

  /**
   * @return \BackupMigrate\Core\File\TempFileManagerInterface;
   */
  public function getTempFileAdapter() {
    return $this->tempFileAdapter;
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