<?php
/**
 * @file
 * Contains BackupMigrate\Core\Exception\IgnorableException
 */


namespace BackupMigrate\Core\Exception;


/**
 * Class IgnorableException
 *
 * This exception can be avoided by setting the 'ignore errors' setting.
 *
 * @package BackupMigrate\Core\Exception
 */
class IgnorableException extends BackupMigrateException {}