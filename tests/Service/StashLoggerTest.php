<?php
/**
 * @file
 */

namespace BackupMigrate\Core\Service;


use Psr\Log\LogLevel;

/**
 * Class StashLoggerTest
 * @package BackupMigrate\Core\Service
 */
class StashLoggerTest extends \PHPUnit_Framework_TestCase {

  /**
   * @covers ::log
   */
  public function testLog() {
    $logger = new StashLogger();

    $logs = [
      ['level' => LogLevel::DEBUG, 'message' => 'Hello, World!'],
      ['level' => LogLevel::ALERT, 'message' => 'Goodnight, Moon!'],
      ['level' => LogLevel::CRITICAL, 'message' => 'I can\'t do that, Dave!'],
      ['level' => LogLevel::EMERGENCY, 'message' => 'DANGER, Will Robinson!']
    ];

    foreach ($logs as $log) {
      $logger->log(
        $log['level'],
        $log['message']
      );
    }

    $stashed = $logger->getAll();
    foreach ($stashed as $i => $log) {
      $this->assertEquals($logs[$i]['level'], $log['level']);
      $this->assertEquals($logs[$i]['message'], $log['message']);
    }
  }
}
