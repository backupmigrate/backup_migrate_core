<?php
/**
 * @file
 * Contains BackupMigrate\Core\Config\ValidationError
 */


namespace BackupMigrate\Core\Config;


/**
 * Class ValidationError
 * @package BackupMigrate\Core\Config
 */
class ValidationError implements ValidationErrorInterface {

  /**
   * @var string
   */
  protected $field_key = '';

  /**
   * @var string
   */
  protected $message = '';

  /**
   * @var array
   */
  protected $replacement = array();

  /**
   * @param $field_key
   * @param $message
   * @param array $replacement
   */
  public function __construct($field_key, $message, $replacement = array()) {
    $this->field_key = $field_key;
    $this->message = $message;
    $this->replacement = $replacement;
  }

  /**
   * @return string
   */
  public function getMessage() {
    return $this->message;
  }

  /**
   * @return array
   */
  public function getReplacement() {
    return $this->replacement;
  }

  /**
   * @return string
   */
  public function getFieldKey() {
    return $this->field_key;
  }

  /**
   * String representation of the exception
   *
   * @return string the string representation of the exception.
   */
  public function __toString()
  {
    return strtr($this->getMessage(), $this->getReplacement());
  }
}