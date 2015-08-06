<?php
/**
 * @file
 */

namespace BackupMigrate\Core\Translation;


/**
 * Interface TranslatableInteface
 * @package BackupMigrate\Core\Translation
 */

interface TranslatableInteface {

  /**
   * Translate a string.
   *
   * @param string $string
   *  The string to be translated.
   * @param $replacements
   *  Any untranslatable variables to be replaced into the string.
   * @param $context
   *  Extra context to help translators distinguish ambiguous strings.
   * @return mixed
   */
  public function t($string, $replacements = array(), $context = array());
}