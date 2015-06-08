<?php
/**
 * @file
 * Contains ${NAMESPACE}\CompressFilter
 */

namespace BackupMigrate\Core\Filter;

use BackupMigrate\Core\Plugin\BackupPluginInterface;
use \BackupMigrate\Core\Plugin\FileProcessorInterface;
use \BackupMigrate\Core\Plugin\FileProcessorTrait;
use \BackupMigrate\Core\Plugin\PluginBase;
use BackupMigrate\Core\Services\TempFileManagerInterface;
use BackupMigrate\Core\Util\BackupFileInterface;
use BackupMigrate\Core\Util\BackupFileReadableInterface;
use BackupMigrate\Core\Util\BackupFileWritableInterface;
use BackupMigrate\Core\Util\TempFile;

/**
 * Class CompressionFilter
 */
class CompressionFilter extends PluginBase implements BackupPluginInterface, FileProcessorInterface {
  use FileProcessorTrait;

  /**
   * Get a list of supported operations and their weight.
   *
   * An array of operations should take the form:
   *
   * array(
   *  'backup' => array('weight' => 100),
   *  'restore' => array('weight' => -100),
   * );
   *
   * @return array
   */
  public function supportedOps() {
    return [
      'backup' => ['weight' => 100],
      'restore' => ['weight' => -100],
    ];
  }

  /**
   * Run on a backup
   *
   * @param \BackupMigrate\Core\Util\BackupFileReadableInterface $file
   * @return \BackupMigrate\Core\Util\BackupFileReadableInterface
   */
  public function backup(BackupFileReadableInterface $file) {
    $out = $this->getTempFileManager()->pushExt($file, 'gz');
    $this->gzipEncode($file, $out);
    return $out;
  }

  /**
   * Gzip decode a file.
   * @param \BackupMigrate\Core\Util\BackupFileReadableInterface $from
   * @param \BackupMigrate\Core\Util\BackupFileWritableInterface $to
   * @return bool
   */
  function gzipEncode(BackupFileReadableInterface $from, BackupFileWritableInterface $to) {
    $success = FALSE;

    if (!$success && @function_exists("gzopen")) {
      if (($fp_out = gzopen($to->realpath(), 'wb9')) && ($fp_in = $from->openForRead())) {
        while (!feof($fp_in)) {
          gzwrite($fp_out, $from->read(1024 * 512));
        }
        $success = TRUE;
        @fclose($fp_in);
        @gzclose($fp_out);
      }
    }

    return $success;
  }


}