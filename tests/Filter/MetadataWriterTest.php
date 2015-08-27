<?php
/**
 * @file
 */

namespace BackupMigrate\Core\Tests\Filter;


use BackupMigrate\Core\Filter\MetadataWriter;
use BackupMigrate\Core\Tests\File\TempFileConsumerTestTrait;

/**
 * Class MetadataWriterTest
 * @package BackupMigrate\Core\Tests\Filter
 */
class MetadataWriterTest extends \PHPUnit_Framework_TestCase {

  use TempFileConsumerTestTrait;

  function setUp() {
    $this->_setUpFiles();
  }


  /**
   * covers ::afterBackup;
   */
  function testFilter() {
    $file = $this->manager->create('txt');

    $metadata = [
      'description' => 'Hello, World!',
      'generator' => 'Backup and Migrate Unit Tests',
      'generatorversion' => '1.0.0'

    ];
    $invalid = [
      'foo' => 'bar',
      'baz' => 'bop',
    ];

    $filter = new MetadataWriter(
      $metadata + $invalid
    );
    $file = $filter->afterBackup($file);


    foreach ($metadata as $key => $value) {
      $this->assertEquals($value, $file->getMeta($key));
    }
    foreach ($invalid as $key => $value) {
      $this->assertNotEquals($value, $file->getMeta($key));
    }
  }
}
