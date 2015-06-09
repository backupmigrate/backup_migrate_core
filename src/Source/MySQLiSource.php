<?php
/**
 * @file
 * Contains BackupMigrate\Core\Source\MySQLiSource
 */


namespace BackupMigrate\Core\Source;


use BackupMigrate\Core\Util\BackupFileReadableInterface;
use BackupMigrate\Core\Util\BackupFileWritableInterface;

/**
 * Class MySQLiSource
 * @package BackupMigrate\Core\Source
 */
class MySQLiSource extends SourceBase {

  /**
   * @var resource A MySQLi connection.
   */
  protected $connection;

  /**
   * Export this source to the given temp file. This should be the main
   * back up function for this source.
   *
   * @return \BackupMigrate\Core\Util\BackupFileReadableInterface $file
   *    A backup file with the contents of the source dumped to it..
   */
  public function exportToFile() {
    $out = NULL;
    if ($connection = $this->_getConnection()) {
      $file = $this->getTempFileManager()->create('mysql');

      $exclude = $this->confGet('exclude_tables');
      $nodata = $this->confGet('nodata_tables');

      $file->write($this->_getSQLHeader());
      $tables = $this->_getTables();

      $lines = 0;
      foreach ($tables as $table) {
      // @TODO reenable this.
//        if (_backup_migrate_check_timeout()) {
//          return FALSE;
//        }
        if ($table['name'] && !isset($exclude[$table['name']])) {
          $file->write($this->_getTableCreateSQL($table));
          $lines++;
          if (!in_array($table['name'], $nodata)) {
            $lines += $this->_dumpTableSQLToFile($file, $table);
          }
        }
      }

      $file->write($this->_getSQLFooter());
      $file->close();
      return $lines;
    }
    else {
      return FALSE;
    }

  }

  /**
   * Import to this source from the given backup file. This is the main restore
   * function for this source.
   *
   * @param \BackupMigrate\Core\Util\BackupFileReadableInterface $file
   *    The file to read the backup from. It will not be opened for reading
   * @return bool|int
   */
  public function importFromFile(BackupFileReadableInterface $file) {
    $num = 0;

    if ($conn = $this->_getConnection()) {
      // Read one line at a time and run the query.
      while ($line = $this->_readSQLCommand($file)) {
//        if (_backup_migrate_check_timeout()) {
//          return FALSE;
//        }
        if ($line) {
          // Execute the sql query from the file.
          $conn->query($line);
          $num++;
        }
      }
      // Close the file, we're done writing to it.
      $file->close();
    }
    return $num;
  }


  /**
   * Get the db connection for the specified db.
   *
   * @return \mysqli Connection object.
   * @throws \Exception
   */
  protected function _getConnection() {
    $this->connection = new \mysqli(
      $this->confGet('host'),
      $this->confGet('user'),
      $this->confGet('password'),
      $this->confGet('database'),
      $this->confGet('port'),
      $this->confGet('socket')
    );
    // Throw an error on fail
    if ($this->connection->connect_errno) {
      throw new \Exception("Failed to connect to MySQL");
    }
    return $this->connection;
  }


  /**
   * Get the header for the top of the SQL file.
   *
   * @return string
   */
  protected function _getSQLHeader() {
    $info = $this->_dbInfo();
    $version = $info['version'];
    $host =  $this->confGet('host');
    $db = $this->confGet('database');
    $timestamp = date('r');

    return <<<HEADER
-- Backup and Migrate MySQL Dump
-- http://github.com/backupmigrate
--
-- Host: $host
-- Database: $db
-- Generation Time: $timestamp
-- MySQL Version: $version

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE=NO_AUTO_VALUE_ON_ZERO */;

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET NAMES utf8;

HEADER;
  }


  /**
   * The footer of the sql dump file.
   */
  function _getSQLFooter() {
    return <<<FOOTER


/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;

FOOTER;
  }

  /**
   * Read a multiline sql command from a file.
   *
   * Supports the formatting created by mysqldump, but won't handle multiline comments.
   * @param \BackupMigrate\Core\Util\BackupFileReadableInterface $file
   * @return string
   */
  function _readSQLCommand(BackupFileReadableInterface $file) {
    $out = '';
    while ($line = $file->readLine()) {
      $first2 = substr($line, 0, 2);
      $first3 = substr($line, 0, 2);

      // Ignore single line comments. This function doesn't support multiline comments or inline comments.
      if ($first2 != '--' && ($first2 != '/*' || $first3 == '/*!')) {
        $out .= ' ' . trim($line);
        // If a line ends in ; or */ it is a sql command.
        if (substr($out, strlen($out) - 1, 1) == ';') {
          return trim($out);
        }
      }
    }
    return trim($out);
  }

  /**
   * Get a list of tables in the database.
   */
  function _getTableNames() {
    $out = array();
    foreach ($this->_getTables() as $table) {
      $out[$table['name']] = $table['name'];
    }
    return $out;
  }

  /**
   * Lock the list of given tables in the database.
   */
  function _lockTables($tables) {
    if ($tables) {
      $tables_escaped = array();
      foreach ($tables as $table) {
        $tables_escaped[] = '`'. $table .'`  WRITE';
      }
      $this->query('LOCK TABLES '. implode(', ', $tables_escaped));
    }
  }

  /**
   * Unlock all tables in the database.
   */
  function _unlockTables($settings) {
    $this->query('UNLOCK TABLES');
  }

  /**
   * Get a list of tables in the db.
   */
  protected function _getTables() {
    $out = array();
    // get auto_increment values and names of all tables
    $tables = $this->query("SHOW TABLE STATUS");
    while ($table = $tables->fetch_assoc()) {
      // Lowercase the keys for consistency.
      $table = array_change_key_case($table);
      $out[$table['name']] = $table;
    }
    return $out;
  }

  /**
   * Get the sql for the structure of the given table.
   *
   * @param array $table
   * @return string
   */
  function _getTableCreateSQL($table) {
    $out = "";

    // If this is a view.
    if (empty($table['engine'])) {
      // Switch SQL mode to get rid of "CREATE ALGORITHM..." which requires more permissions and has trouble with the DEFINER user
      $sql_mode  = $this->query("SELECT @@SESSION.sql_mode")->fetch_field();
      $this->query("SET sql_mode = 'ANSI'");
      $result = $this->query("SHOW CREATE VIEW `" . $view['name'] . "`");

      while ($create = $result->fetch_assoc()) {
        // Lowercase the keys for consistency
        $create = array_change_key_case($create);
        $out .= "DROP VIEW IF EXISTS `". $view['name'] ."`;\n";
        $out .= "SET sql_mode = 'ANSI';\n";
        $out .= strtr($create['create view'], "\n", " ") . ";\n";
        $out .= "SET sql_mode = '$sql_mode';\n";
      }

      // Set the SQL_mode back to the original value.
      $this->query("SET SQL_mode = '$sql_mode'");
    }

    // This is a regular table.
    else {
      $result = $this->query("SHOW CREATE TABLE `". $table['name'] ."`");
      while ($create = $result->fetch_assoc()) {
        // Lowercase the keys for consistency.
        $create = array_change_key_case($create);
        $out .= "DROP TABLE IF EXISTS `". $table['name'] ."`;\n";
        // Remove newlines
        $out .= strtr($create['create table'], array("\n" => ' '));
        $out .= $create['create table'];
        if ($table['auto_increment']) {
          $out .= " AUTO_INCREMENT=". $table['auto_increment'];
        }
        $out .= ";\n";
      }
    }

    return $out;
  }

  /**
   *  Get the sql to insert the data for a given table
   */
  function _dumpTableSQLToFile(BackupFileWritableInterface $file, $table) {

    // If this is a view, do not export any data
    if (empty('engine')) {
      return 0;
    }

    // Otherwise export the table data.
    $rows_per_line  = 30; //$this->confGet('rows_per_line');//variable_get('backup_migrate_data_rows_per_line', 30);
    $bytes_per_line = 2000; //$this->confGet('bytes_per_line'); variable_get('backup_migrate_data_bytes_per_line', 2000);

    $lines = 0;
    $result = $this->query("SELECT * FROM `". $table['name'] ."`");
    $rows = $bytes = 0;

    // Escape backslashes, PHP code, special chars
    $search = array('\\', "'", "\x00", "\x0a", "\x0d", "\x1a");
    $replace = array('\\\\', "''", '\0', '\n', '\r', '\Z');

    while ($row = $result->fetch_assoc()) {
      // DB Escape the values.
      $items = array();
      foreach ($row as $key => $value) {
        $items[] = is_null($value) ? "null" : "'". str_replace($search, $replace, $value) ."'";
        // @TODO: escape binary data
      }

      // If there is a row to be added.
      if ($items) {
        // Start a new line if we need to.
        if ($rows == 0) {
          $file->write("INSERT INTO `". $table['name'] ."` VALUES ");
          $bytes = $rows = 0;
        }
        // Otherwise add a comma to end the previous entry.
        else {
          $file->write(",");
        }

        // Write the data itself.
        $sql = implode(',', $items);
        $file->write('('. $sql .')');
        $bytes += strlen($sql);
        $rows++;

        // Finish the last line if we've added enough items
        if ($rows >= $rows_per_line || $bytes >= $bytes_per_line) {
          $file->write(";\n");
          $lines++;
          $bytes = $rows = 0;
        }
      }
    }
    // Finish any unfinished insert statements.
    if ($rows > 0) {
      $file->write(";\n");
      $lines++;
    }

    return $lines;
  }


  /**
   * Run a db query on this destination's db.
   */
  function query($query, $args = array(), $options = array()) {
    if ($conn = $this->_getConnection()) {
      return $conn->query($query);
    }
  }

  /**
   * Get the version info for the given DB.
   */
  function _dbInfo() {
    $conn = $this->_getConnection();
    return array(
      'type' => 'mysql',
      'version' => $conn->server_version,
    );
  }


}