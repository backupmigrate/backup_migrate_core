<?php
/**
 * @file
 * Contains BackupMigrate\Core\Filter\Notify
 */


namespace BackupMigrate\Core\Filter;


use BackupMigrate\Core\Plugin\PluginBase;

/**
 * Class Notify
 *
 * Notifies by email when a backup succeeds or fails.
 *
 * @package BackupMigrate\Core\Filter
 */
class Notify extends PluginBase {

  // @TODO: Add a tee to the logger to capture all messages.

  // @TODO: Implement backup/restore fail/succeed ops and send a notification.
}