<?php
use BackupMigrate\Core\Config\Config;
use BackupMigrate\Core\File\TempFileAdapter;
use BackupMigrate\Core\File\TempFileManager;
use BackupMigrate\Core\Source\MySQLiSource;
use BackupMigrate\Core\Tests\File\TempFileConsumerTestTrait;
use org\bovigo\vfs\vfsStream;

/**
 * @file
 */

/**
 * Class MySQLiSourceTest
 */
class MySQLiSourceTest extends \PHPUnit_Extensions_Database_TestCase {
  use TempFileConsumerTestTrait;

  static private $pdo = NULL;
  private $conn = NULL;

  /**
   * @var \BackupMigrate\Core\Source\MySQLiSource
   */
  private $source = NULL;


  /**
   * @var array
   */
  protected $data;

  /**
   * Returns the test database connection.
   *
   * @return PHPUnit_Extensions_Database_DB_IDatabaseConnection
   */
  public function getConnection() {
    if (!extension_loaded('mysqli') || empty($GLOBALS['DB_DBNAME'])) {
      $this->markTestSkipped(
        'The MySQLi extension is not available.'
      );
    }

    $dsn = "mysql:dbname=$GLOBALS[DB_DBNAME];host=$GLOBALS[DB_HOST];port=$GLOBALS[DB_PORT];charset=UTF8";

    try {
      if ($this->conn === NULL) {
        if (self::$pdo == NULL) {
          self::$pdo = new PDO(
            $dsn,
            $GLOBALS['DB_USER'],
            $GLOBALS['DB_PASSWD']
          );
        }
        $this->conn = $this->createDefaultDBConnection(self::$pdo, $GLOBALS['DB_DBNAME']);
      }
    }
    catch (Exception $e) {
      $this->markTestSkipped(
        'Could not connect to the testing MySQL database.'
      );
    }

    return $this->conn;
  }

  /**
   * Returns the test dataset.
   *
   * @return PHPUnit_Extensions_Database_DataSet_IDataSet
   */
  public function getDataSet() {
    $this->data = [
      'table1' => [
        ['id' => 1, 'field1' => 'Hello, World!', 'field2' => NULL],
        ['id' => 2, 'field1' => '晚安，月亮', 'field2' => NULL],
      ],
      'table2' => [
        ['id' => 1, 'field1' => '1980-09-11'],
      ]
    ];
    return $this->createArrayDataSet($this->data);
  }

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();

    $this->_setUpFiles();

    $this->source = new MySQLiSource(new Config(
      [
        'host' => $GLOBALS['DB_HOST'],
        'database' => $GLOBALS['DB_DBNAME'],
        'username' => $GLOBALS['DB_USER'],
        'password' => $GLOBALS['DB_PASSWD'],
        'port' => $GLOBALS['DB_PORT'],
      ]
    ));

    $this->root = vfsStream::setup('root', 0777, ['tmp' => []]);
    $this->adapter = new TempFileAdapter($this->root->url() . '/tmp/', 'abc');
    $this->manager = new TempFileManager($this->adapter);

    $this->source->setTempFileManager($this->manager);
  }
  /**
   * @covers ::getTableNames()
   */
  function testGetTableNames() {
    $tables = $this->source->getTableNames();

    $this->assertEquals(3, count($tables));
    $this->assertContains('view1', $tables);
    $this->assertContains('table1', $tables);
    $this->assertContains('table2', $tables);
  }

  /**
   * @covers ::getTables();
   */
  function testGetTables() {
    $tables = $this->source->getTables();

    $this->assertEquals(3, count($tables));
    foreach ($tables as $table) {
      $this->assertArrayHasKey('name', $table);
    }
  }

  /**
   * @covers ::textExportToFile
   */
  function testExportStructure() {
    $file = $this->source->exportToFile();

    $this->assertStringEndsWith('.mysql', $file->getFullName());
    $dump = $file->readAll();

    // Check the header
    $this->assertContains("-- Backup and Migrate MySQL Dump", $dump);
    $this->assertContains("-- Database: " . $GLOBALS['DB_DBNAME'], $dump);
    $this->assertContains("SET NAMES utf8;", $dump);
    $this->assertContains("/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE=NO_AUTO_VALUE_ON_ZERO */;", $dump);

    // Check the footer
    $this->assertContains("/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;", $dump);

    // Check the table structure
    $this->assertContains("DROP TABLE IF EXISTS `table1`", $dump);
    $this->assertContains("CREATE TABLE `table1`", $dump);
    $this->assertContains("DROP TABLE IF EXISTS `table2`", $dump);
    $this->assertContains("CREATE TABLE `table2`", $dump);

    // Check that views were exported.
//    $this->assertContains("CREATE VIEW `view1`", $dump);

  }

  /**
   * @covers ::exportToFile
   */
  function testExportTableData() {
    $this->assertEquals(2, $this->getConnection()->getRowCount('table1'), "Pre-Condition");
    $this->assertEquals(1, $this->getConnection()->getRowCount('table2'), "Pre-Condition");

    $file = $this->source->exportToFile();
    $dump = $file->readAll();

    $this->assertContains("INSERT INTO `table1` VALUES ('1','Hello, World!',null),('2','晚安，月亮',null);", $dump);
    $this->assertContains("INSERT INTO `table2` VALUES ('1','1980-09-11');", $dump);
  }

  /**
   * @covers ::exportToFile
   */
  function testExportBinaryField() {
    // @TODO: this.
  }

  /**
   * @covers ::importFromFile
   */
  function testImportFromFile() {
    $this->assertTablesEqual(
      $this->createArrayDataSet($this->data)->getTable("table1"),
      $this->getConnection()->createQueryTable('table1', 'SELECT * FROM table1')
    );

    // Test that sql statements are executed:
    $file = $this->manager->create('mysql');
    $file->writeAll("INSERT INTO `table1` VALUES ('3','I am a record!',null);");
    $this->source->importFromFile($file);
    $this->data['table1'][] = [
      'id' => '3',
      'field1' => 'I am a record!',
      'field2' => NULL
    ];
    $this->assertTablesEqual(
      $this->createArrayDataSet($this->data)->getTable("table1"),
      $this->getConnection()->createQueryTable('table1', 'SELECT * FROM table1')
    );
  }

  /**
   * @covers ::importFromFile
   */
  function testImportMultipleStatements() {
    // Test multiple statements
    $file = $this->manager->create('mysql');
    $file->write("INSERT INTO `table1` VALUES ('3','I am a record!',null);\n");
    // Make sure that blank lines are ignored correctly.
    $file->write("\n");
    $file->write("INSERT INTO `table1` VALUES ('4','I am a fifth record!',null);");
    $file->close();
    $this->source->importFromFile($file);
    $this->data['table1'][] = [
      'id' => '3',
      'field1' => 'I am a record!',
      'field2' => NULL
    ];
    $this->data['table1'][] = [
      'id' => '4',
      'field1' => 'I am a fifth record!',
      'field2' => NULL
    ];
    $this->assertTablesEqual(
      $this->createArrayDataSet($this->data)->getTable("table1"),
      $this->getConnection()->createQueryTable('table1', 'SELECT * FROM table1')
    );
  }

  /**
   * @covers ::importFromFile
   */
  function testImportMultilineStatements() {
    // Test multi-line statements
    $file = $this->manager->create('mysql');
    $file->writeAll("INSERT INTO `table1`\nVALUES ('3','I am a record!',null);");
    $this->source->importFromFile($file);
    $this->data['table1'][] = [
      'id' => '3',
      'field1' => 'I am a record!',
      'field2' => NULL
    ];
    $this->assertTablesEqual(
      $this->createArrayDataSet($this->data)->getTable("table1"),
      $this->getConnection()->createQueryTable('table1', 'SELECT * FROM table1')
    );
  }

  /**
   * @covers ::importFromFile
   */
  function testImportComments() {
    // Test that comments are ignored:
    $file = $this->manager->create('mysql');
    $file->writeAll("-- INSERT INTO `table1` VALUES ('6','I am a record!',null);");
    $this->source->importFromFile($file);
    $file = $this->manager->create('mysql');
    $file->writeAll("/* INSERT INTO `table1` VALUES ('7','I am a record!',null); */");
    $this->source->importFromFile($file);
    $this->assertTablesEqual(
      $this->createArrayDataSet($this->data)->getTable("table1"),
      $this->getConnection()->createQueryTable('table1', 'SELECT * FROM table1')
    );
  }


}
