<?php
/**
 * @file
 * Contains BackupMigrate\Core\Filter\CompressFilter
 */

namespace BackupMigrate\Core\Filter;

use BackupMigrate\Core\Plugin\FileProcessorInterface;
use BackupMigrate\Core\Plugin\FileProcessorTrait;
use BackupMigrate\Core\Plugin\PluginBase;
use BackupMigrate\Core\Util\BackupFileReadableInterface;
use BackupMigrate\Core\Util\BackupFileWritableInterface;

/**
 * Class CompressionFilter
 */
class CompressionFilter extends PluginBase implements FileProcessorInterface {
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
      'getFileTypes'    => [],
      'backupSettings'  => [],
      'afterBackup'     => ['weight' => 100],
      'beforeRestore'   => ['weight' => -100],
    ];
  }

  /**
   * Return the filetypes supported by this filter.
   */
  public function getFileTypes() {
    return array(
      array(
        "gzip" => array(
          "extension" => "gz",
          "filemime" => "application/x-gzip",
          'ops' => array(
            'backup',
            'restore'
          )
        ),
        "bzip" => array(
          "extension" => "bz",
          "filemime" => "application/x-bzip",
          'ops' => array(
            'backup',
            'restore'
          )
        ),
        "bzip2" => array(
          "extension" => "bz2",
          "filemime" => "application/x-bzip",
          'ops' => array(
            'backup',
            'restore'
          )
        ),
        "zip" => array(
          "extension" => "zip",
          "filemime" => "application/zip",
          'ops' => array(
            'backup',
            'restore'
          )
        ),
      ),
    );
  }

  /**
   * Get a definition for user-configurable settings.
   *
   * @return array
   */
  public function backupSettings() {
    $form = array();

    $compression_options = $this->_availableCompressionAlgorithms();
    $form['fields']['compression'] = array(
      '#group' => 'file',
      "#type" => count($compression_options) > 1 ? "select" : 'value',
      "#title" => 'Compression',
      "#options" => $compression_options,
    );

    return $form;
  }

  /**
   * Run on a backup
   *
   * @param \BackupMigrate\Core\Util\BackupFileReadableInterface $file
   * @return \BackupMigrate\Core\Util\BackupFileReadableInterface
   */
  public function afterBackup(BackupFileReadableInterface $file) {
    // If no compression is specified then return the input file as is.
    $out = $file;
    if ($this->confGet('compression') == 'gzip') {
      $out = $this->getTempFileManager()->pushExt($file, 'gz');
      $this->_gzipEncode($file, $out);
    }
    if ($this->confGet('compression') == 'bzip') {
      $out = $this->getTempFileManager()->pushExt($file, 'bz2');
      $this->_bzipEncode($file, $out);
    }
    if ($this->confGet('compression') == 'zip') {
      $out = $this->getTempFileManager()->pushExt($file, 'zip');
      $this->_ZipEncode($file, $out);
    }
    return $out;
  }

  /**
   * Run on a restore
   *
   * @param \BackupMigrate\Core\Util\BackupFileReadableInterface $file
   * @return \BackupMigrate\Core\Util\BackupFileReadableInterface
   */
  public function beforeRestore(BackupFileReadableInterface $file) {
    // If the file is not a supported compression type then simply return the same input file.
    $out = $file;

    $type = $file->getExtLast();
    switch (strtolower($type)) {
      case "gz":
      case "gzip":
        $out = $this->getTempFileManager()->popExt($file);
        $this->_gzipDecode($file, $out);
        break;

      case "bz":
      case "bz2":
      case "bzip":
      case "bzip2":
        $out = $this->getTempFileManager()->popExt($file);
        $this->_bzipDecode($file, $out);
        break;

      case "zip":
        $out = $this->getTempFileManager()->popExt($file);
        $this->_ZipDecode($file, $out);
        break;

    }
    return $out;
  }


  /**
   * Gzip encode a file.
   *
   * @param \BackupMigrate\Core\Util\BackupFileReadableInterface $from
   * @param \BackupMigrate\Core\Util\BackupFileWritableInterface $to
   * @return bool
   */
  protected function _gzipEncode(BackupFileReadableInterface $from, BackupFileWritableInterface $to) {
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

  /**
   * Gzip decode a file.
   *
   * @param \BackupMigrate\Core\Util\BackupFileReadableInterface $from
   * @param \BackupMigrate\Core\Util\BackupFileWritableInterface $to
   * @return bool
   */
  protected function _gzipDecode(BackupFileReadableInterface $from, BackupFileWritableInterface $to) {
    $success = FALSE;

    if (!$success && @function_exists("gzopen")) {
      if (($fp_in = gzopen($from->realpath(), 'rb9'))) {
        while (!feof($fp_in)) {
          $to->write(gzread($fp_in, 1024 * 512));
        }
        $success = TRUE;
        @gzclose($fp_in);
        $to->close();
      }
    }

    return $success;
  }

  /**
   * BZip encode a file.
   *
   * @param \BackupMigrate\Core\Util\BackupFileReadableInterface $from
   * @param \BackupMigrate\Core\Util\BackupFileWritableInterface $to
   * @return bool
   */
  protected function _bzipEncode(BackupFileReadableInterface $from, BackupFileWritableInterface $to) {
    $success = FALSE;

    if (!$success && @function_exists("bzopen")) {
      if (($fp_out = bzopen($to->realpath(), 'wb')) && ($fp_in = $from->openForRead())) {
        while (!feof($fp_in)) {
          bzwrite($fp_out, $from->read(1024 * 512));
        }
        $success = TRUE;
        @fclose($fp_in);
        @gzclose($fp_out);
      }
    }

    return $success;
  }

  /**
   * BZip decode a file.
   *
   * @param \BackupMigrate\Core\Util\BackupFileReadableInterface $from
   * @param \BackupMigrate\Core\Util\BackupFileWritableInterface $to
   * @return bool
   */
  protected function _bzipDecode(BackupFileReadableInterface $from, BackupFileWritableInterface $to) {
    $success = FALSE;

    if (!$success && @function_exists("bzopen")) {
      if (($fp_in = gzopen($from->realpath(), 'rb9'))) {
        while (!feof($fp_in)) {
          $to->write(bzread($fp_in, 1024 * 512));
        }
        $success = TRUE;
        @bzclose($fp_in);
        $to->close();
      }
    }

    return $success;
  }

  /**
   * Gzip encode a file.
   *
   * @param \BackupMigrate\Core\Util\BackupFileReadableInterface $from
   * @param \BackupMigrate\Core\Util\BackupFileWritableInterface $to
   * @return bool
   */
  protected function _ZipEncode(BackupFileReadableInterface $from, BackupFileWritableInterface $to) {
    $success = FALSE;

    if (class_exists('ZipArchive')) {
      $zip = new \ZipArchive;
      if ($zip->open($from->realpath())) {
        $filename = ($zip->getNameIndex(0));
        if ($fp_in = $zip->getStream($filename)) {
          while (!feof($fp_in)) {
            $to->write(fread($fp_in, 1024 * 512));
          }
          @fclose($fp_in);
          $success = TRUE;
        }
        @fclose($fp_out);
      }
    }

    return $success;
  }

  /**
   * Gzip decode a file.
   *
   * @param \BackupMigrate\Core\Util\BackupFileReadableInterface $from
   * @param \BackupMigrate\Core\Util\BackupFileWritableInterface $to
   * @return bool
   */
  protected function _ZipDecode(BackupFileReadableInterface $from, BackupFileWritableInterface $to) {
    $success = FALSE;

    if (class_exists('ZipArchive')) {
      $zip = new \ZipArchive;
      $res = $zip->open($to->realpath(), constant("ZipArchive::CREATE"));
      if ($res === TRUE) {
        $zip->addFile($from->realpath(), $from->getMeta('filename'));
        $success = $zip->close();
      }
    }

    return $success;
  }


  /**
   * Get the default value for the compression type.
   *
   * @return string
   */
  protected function _confDefault_compression() {
    $available = array_keys($this->_availableCompressionAlgorithms());
    // Remove the 'none' option.
    array_shift($available);
    $out = array_shift($available);
    // Return the first available algorithm or 'none' of none other exist.
    return $out ? $out : 'none';
  }

  /**
   * Get the compression options as an options array for a form item.
   *
   * @return array
   */
  protected function _availableCompressionAlgorithms() {
    $compression_options = array("none" => t("No Compression"));
    if (@function_exists("gzencode")) {
      $compression_options['gzip'] = t("GZip");
    }
    if (@function_exists("bzcompress")) {
      $compression_options['bzip'] = t("BZip");
    }
    if (class_exists('ZipArchive')) {
      $compression_options['zip'] = t("Zip", array(), array('context' => 'compression format'));
    }
    return $compression_options;
  }


}