<?php

/**
 * @file
 * Contains \BackupMigrate\Core\Destination\DestinationInterface.
 */

namespace BackupMigrate\Core\Destination;

use BackupMigrate\Core\Exception\DestinationNotWritableException;
use BackupMigrate\Core\File\BackupFileInterface;
use BackupMigrate\Core\File\BackupFileReadableInterface;
use BackupMigrate\Core\Plugin\PluginInterface;

/**
 * Provides an interface defining a backup destination (ie: a place where backup
 * files are stored).
 */
interface DestinationInterface extends PluginInterface
{

}
