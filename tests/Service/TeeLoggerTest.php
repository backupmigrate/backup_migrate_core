<?php
/**
 * @file
 */

namespace BackupMigrate\Core\Service;


/**
 * Class TeeLoggerTest
 * @package BackupMigrate\Core\Tests\Service
 */
class TeeLoggerTest extends \PHPUnit_Framework_TestCase {


  /**
   * @covers ::construct
   * @covers ::log
   */
  public function testLog() {
    $logger1 = $this->getMockBuilder('\Psr\Log\AbstractLogger')
      ->setMethods(['log'])
      ->getMock();
    $logger2 = $this->getMockBuilder('\Psr\Log\AbstractLogger')
      ->setMethods(['log'])
      ->getMock();

    $logger1->expects($this->once())->method('log')
      ->with(
        \Psr\Log\LogLevel::DEBUG, 'Hello, World'
      );
    $logger2->expects($this->once())
      ->method('log')
      ->with(
        \Psr\Log\LogLevel::DEBUG, 'Hello, World'
      );

    $tee = new TeeLogger([$logger1, $logger2]);
    $tee->log(\Psr\Log\LogLevel::DEBUG, 'Hello, World');
  }
}
