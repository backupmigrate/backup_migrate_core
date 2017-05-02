<?php

/**
 * @file
 * Contains \BackupMigrate\Core\Destination\DestinationInterface.
 */

namespace BackupMigrate\Core\Destination;

use BackupMigrate\Core\Plugin\PluginInterface;

/**
 * Provides an interface defining a backup destination (ie: a place where backup
 * files are stored).
 */
interface DestinationInterface extends PluginInterface
{

}
