<?php
/**
 * @file
 */

namespace BackupMigrate\Core\Config;


/**
 * Interface ValidationErrorInterface
 * @package BackupMigrate\Core\Config
 */
interface ValidationErrorInterface {

  /**
   * @return string
   */
  public function getMessage();

  /**
   * @return array
   */
  public function getReplacement();

  /**
   * @return string
   */
  public function getFieldKey();
}