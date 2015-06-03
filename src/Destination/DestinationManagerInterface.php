<?php

/**
 * @file
 * Contains \BackupMigrate\Core\Destination\DestinationManagerInterface.
 */

namespace BackupMigrate\Core\Destination;

/**
 * Manage all of the available destinations.
 */
interface DestinationManagerInterface
{
  /**
   * Add an available destination
   * 
   * @param \BackupMigrate\Core\Destination\DestinationInterface $destination 
   *    The destination to add.
   * @param string $destination_id
   *   Identifier of the provider.
   * @param int $weight
   *   (optional) The the order of the destination when it appears in lists.
   */
  public function add(DestinationInterface $destination, $destination_id, $weight = 0);

  /**
   * Get the destination with the given id.
   * 
   * @param string $destination_id The id of the destination to return
   * 
   * @return DestinationInterface The destination specified by the id or NULL if it doesn't exist.
   */
  public function get($destination_id);

  /**
   * Get a list of all of the destinations.
   *
   * @return array An ordered list of the destinations, keyed by their id.
   */
  public function getAll();
}
