<?php
/**
 * @file
 * Contains BackupMigrate\Core\Filter\Notify
 */


namespace BackupMigrate\Core\Filter;


use BackupMigrate\Core\Plugin\PluginBase;
use BackupMigrate\Core\Plugin\PluginCallerInterface;
use BackupMigrate\Core\Plugin\PluginCallerTrait;
use BackupMigrate\Core\Service\StashLogger;
use BackupMigrate\Core\Service\TeeLogger;

/**
 * Class Notify
 *
 * Notifies by email when a backup succeeds or fails.
 *
 * @package BackupMigrate\Core\Filter
 */
class Notify extends PluginBase implements PluginCallerInterface {
  use PluginCallerTrait;

  /**
   * Add a weight so that our before* operations run before any other plugin has
   * a chance to write any log entries.
   *
   * @return array
   */
  public function supportedOps() {
    return [
      'beforeBackup' => ['weight' => -100000],
      'beforeRestore' => ['weight' => -100000],
    ];
  }

  /**
   * @var StashLogger
   */
  protected $logstash;

  public function beforeBackup() {
    $this->addLogger();
  }

  public function beforeRestore() {
    $this->addLogger();
  }

  public function backupSucceed() {
    $this->sendNotification('Backup finished sucessfully');
  }

  public function backupFail(Exception $e) {

  }

  public function restoreSucceed() {
  }

  public function restoreFail() {
  }

  /**
   * @param $subject
   * @param $body
   * @param $messages
   */
  protected function sendNotification($subject) {
    $messages = $this->logstash->getAll();
    $body = $subject . "\n";
    if (count($messages)) {

    }
    // $body .=
  }

  /**
   * add our stash logger to the service locator to capture all logged messages.
   */
  protected function addLogger() {
    $services = $this->plugins()->services();

    // Get the current logger.
    $logger = $services->get('Logger');

    // Create a new stash logger to save messages.
    $this->logstash = new StashLogger();

    // Add a tee to send logs to both the regular logger and our stash.
    $services->add('Logger', new TeeLogger([$logger, $this->logstash]));

    // Add the services back into the plugin manager to re-inject existing plugins
    $this->plugins()->setServiceLocator($services);
  }

  // @TODO: Add a tee to the logger to capture all messages.
  // @TODO: Implement backup/restore fail/succeed ops and send a notification.
}