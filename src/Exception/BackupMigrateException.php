<?php
/**
 * @file
 * Contains ${NAMESPACE}\BackupMirgateException
 */

namespace BackupMigrate\Core\Exception;

use Exception;

/**
 * Class BackupMigrateException
 * @package BackupMigrate\Core\Exception
 */
class BackupMigrateException extends Exception {
  protected $message = 'Unknown exception';
  protected $replacement = array();

  /**
   * Construct the exception. Note: The message is NOT binary safe.
   *
   * @link http://php.net/manual/en/exception.construct.php
   * @param string $message [optional] The Exception message to throw.
   * @param array $replacement [optional] Untranslatable values to replace into the string.
   * @param int $code [optional] The Exception code.
   */
  public function __construct($message = null, $replacement = array(), $code = 0)
  {
    $this->replacement = $replacement;
    parent::__construct($message, $code);
  }

  /**
   * String representation of the exception
   *
   * @link http://php.net/manual/en/exception.tostring.php
   * @return string the string representation of the exception.
   */
  public function __toString()
  {
    return strtr($this->message, $this->replacement);
  }
}