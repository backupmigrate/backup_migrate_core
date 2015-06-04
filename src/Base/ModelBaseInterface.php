<?php
/**
 * @file
 * Contains \BackupMigrate\Core\Base\ModelBaseInterface.
 */
namespace BackupMigrate\Core\Base;

interface ModelBaseInterface {
  /**
   * Load an existing item from an array.
   */
  public function fromArray($params);

  /**
   * Return as an array of values.
   */
  function toArray();
}