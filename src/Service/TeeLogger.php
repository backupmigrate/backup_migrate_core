<?php
/**
 * @file
 * Contains BackupMigrate\Core\Tests\Service\TeeLogger
 */


namespace BackupMigrate\Core\Service;


use Psr\Log\AbstractLogger;
use Psr\Log\LoggerInterface;

/**
 * Class TeeLogger
 * @package BackupMigrate\Core\Tests\Service
 */
class TeeLogger extends AbstractLogger {

  /**
   * @var LoggerInterface[]
   */
  protected $loggers;

  /**
   * @param \Psr\Log\LoggerInterface[] $loggers
   */
  public function __construct($loggers) {
    $this->setLoggers($loggers);
  }

  /**
   * Logs with an arbitrary level.
   *
   * @param mixed $level
   * @param string $message
   * @param array $context
   *
   * @return null
   */
  public function log($level, $message, array $context = array()) {
    foreach ($this->getLoggers() as $logger) {
      $logger->log($level, $message, $context);
    }
  }

  /**
   * @return \Psr\Log\LoggerInterface[]
   */
  public function getLoggers() {
    return $this->loggers;
  }

  /**
   * @param \Psr\Log\LoggerInterface[] $loggers
   */
  public function setLoggers($loggers) {
    $this->loggers = $loggers;
  }

  /**
   * @param \Psr\Log\LoggerInterface $logger
   */
  public function addLogger(LoggerInterface $logger) {
    $this->loggers[] = $logger;
  }
}