<?php

/**
 * @file
 * Contains \BackupMigrate\Core\Source\DatabaseSource.
 */


// Must be injected:
// Database access (PDO object etc.)
//  Takes a set of credentials
//  Allows raw queries
//  Queries return a list of assoc arrays

namespace BackupMigrate\Core\Source;

class DatabaseSource extends SourceBase
{
  /**
   * Database connection credentials.
   *
   * @var string
   */
  protected $id;

}
