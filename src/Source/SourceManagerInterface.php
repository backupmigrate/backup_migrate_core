<?php

/**
 * @file
 * Contains \BackupMigrate\Core\Source\SourceManagerInterface.
 */

namespace BackupMigrate\Core\Source;

/**
 * Manage all of the available sources.
 */
interface SourceManagerInterface
{
  /**
   * Add an available source
   * 
   * @param \BackupMigrate\Core\Source\SourceInterface $source 
   *    The source to add.
   * @param string $source_id
   *   Identifier of the provider.
   * @param int $weight
   *   (optional) The the order of the source when it appears in lists.
   */
  public function add(SourceInterface $source, $source_id, $weight = 0);

  /**
   * Get the source with the given id.
   * 
   * @param string $source_id The id of the source to return
   * 
   * @return SourceInterface The source specified by the id or NULL if it doesn't exist.
   */
  public function get($source_id);

  /**
   * Get a list of all of the sources.
   *
   * @return array An ordered list of the sources, keyed by their id.
   */
  public function getAll();
}
