<?php

/**
 * @file
 * Contains \BackupMigrate\Core\Services\DatabaseConnectionInterface.
 */

namespace BackupMigrate\Core\Services;

/**
 * Provides a service to provision temp files in the correct place for the environment.
 */
interface DatabaseConnectionInterface {
  /**
   * Can we connect to this database
   * 
   * @return boolean True if we can reach the db, false otherwise.
   */
  public function canConnect();

  /**
   * Connect to the database
   * 
   * @return boolean True if the connection was successful, false otherwise.
   */
  public function connect();

  /**
   * Disconnect from database
   */
  public function disconnect();

  /**
   * Send a query to the database
   * 
   * @return string The query to send. Must be already escaped.
   */
  public function query($query);

}
