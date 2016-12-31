<?php
/**
 * @file
 * Contains BackupMigrate\Core\Filter\CompressFilter
 */

namespace BackupMigrate\Core\Filter;

use BackupMigrate\Core\Config\Config;
use BackupMigrate\Core\Plugin\FileProcessorInterface;
use BackupMigrate\Core\Plugin\FileProcessorTrait;
use BackupMigrate\Core\Plugin\PluginBase;
use BackupMigrate\Core\File\BackupFileReadableInterface;
use BackupMigrate\Core\File\BackupFileWritableInterface;

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
  public function configSchema($params = array()) {
    $schema = array();

    if ($params['operation'] == 'backup') {
      $schema['groups']['file'] = [
        'title' => 'Backup File',
      ];
      $compression_options = $this->_availableCompressionAlgorithms();
      $schema['fields']['compression'] = [
        'group' => 'file',
        'type' => 'enum',
        'title' => 'Compression',
        'options' => $compression_options,
        'actions' => ['backup']
      ];
    }

    return $schema;
  }


  /**
   * Get the default values for the plugin.
   *
   * @return \BackupMigrate\Core\Config\Config
   */
  public function configDefaults() {
    return new Config([
      'compression' => $this->_defaultCompressionAlgorithm(),
    ]);
  }


  /**
   * Run on a backup
   *
   * @param \BackupMigrate\Core\File\BackupFileReadableInterface $file
   * @return \BackupMigrate\Core\File\BackupFileReadableInterface
   */
  public function afterBackup(BackupFileReadableInterface $file) {
    $out = $success = FALSE;
    if ($this->confGet('compression') == 'gzip') {
      $out = $this->getTempFileManager()->pushExt($file, 'gz');
      $success = $this->_gzipEncode($file, $out);
    }
    if ($this->confGet('compression') == 'bzip') {
      $out = $this->getTempFileManager()->pushExt($file, 'bz2');
      $success = $this->_bzipEncode($file, $out);
    }
    if ($this->confGet('compression') == 'zip') {
      $out = $this->getTempFileManager()->pushExt($file, 'zip');
      $success = $this->_ZipEncode($file, $out);
    }

    // If the file was successfully compressed.
    if ($out && $success) {
      $out->setMeta('filesize_uncompressed', $file->getMeta('filesize'));
      $out->setMeta('compression', $this->confGet('compression'));
      return $out;
    }

    // Return the original if we were not able to compress it.
    return $file;
  }

  /**
   * Run on a restore
   *
   * @param \BackupMigrate\Core\File\BackupFileReadableInterface $file
   * @return \BackupMigrate\Core\File\BackupFileReadableInterface
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
   * @param \BackupMigrate\Core\File\BackupFileReadableInterface $from
   * @param \BackupMigrate\Core\File\BackupFileWritableInterface $to
   * @return bool
   */
  protected function _gzipEncode(BackupFileReadableInterface $from, BackupFileWritableInterface $to) {
    $success = FALSE;

    if (!$success && function_exists("gzopen")) {
      if (($fp_out = gzopen($to->realpath(), 'wb9')) && $from->openForRead()) {
        while ($data = $from->readBytes(1024 * 512)) {
          gzwrite($fp_out, $data);
        }
        $success = TRUE;
        $from->close();
        gzclose($fp_out);
      }
    }

    return $success;
  }

  /**
   * Gzip decode a file.
   *
   * @param \BackupMigrate\Core\File\BackupFileReadableInterface $from
   * @param \BackupMigrate\Core\File\BackupFileWritableInterface $to
   * @return bool
   */
  protected function _gzipDecode(BackupFileReadableInterface $from, BackupFileWritableInterface $to) {
    $success = FALSE;

    if (!$success && function_exists("gzopen")) {
      if ($fp_in = gzopen($from->realpath(), 'rb9')) {
        while (!feof($fp_in)) {
          $to->write(gzread($fp_in, 1024 * 512));
        }
        $success = TRUE;
        gzclose($fp_in);
        $to->close();
      }
    }

    return $success;
  }

  /**
   * BZip encode a file.
   *
   * @param \BackupMigrate\Core\File\BackupFileReadableInterface $from
   * @param \BackupMigrate\Core\File\BackupFileWritableInterface $to
   * @return bool
   */
  protected function _bzipEncode(BackupFileReadableInterface $from, BackupFileWritableInterface $to) {
    $success = FALSE;
    if (!$success && function_exists("bzopen")) {
      if (($fp_out = bzopen($to->realpath(), 'w')) && $from->openForRead()) {
        while ($data = $from->readBytes(1024 * 512)) {
          bzwrite($fp_out, $data);
        }
        $success = TRUE;
        $from->close();
        bzclose($fp_out);
      }
    }

    return $success;
  }

  /**
   * BZip decode a file.
   *
   * @param \BackupMigrate\Core\File\BackupFileReadableInterface $from
   * @param \BackupMigrate\Core\File\BackupFileWritableInterface $to
   * @return bool
   */
  protected function _bzipDecode(BackupFileReadableInterface $from, BackupFileWritableInterface $to) {
    $success = FALSE;

    if (!$success && function_exists("bzopen")) {
      if ($fp_in = bzopen($from->realpath(), 'r')) {
        while (!feof($fp_in)) {
          $to->write(bzread($fp_in, 1024 * 512));
        }
        $success = TRUE;
        bzclose($fp_in);
        $to->close();
      }
    }

    return $success;
  }

  /**
   * Gzip encode a file.
   *
   * @param \BackupMigrate\Core\File\BackupFileReadableInterface $from
   * @param \BackupMigrate\Core\File\BackupFileWritableInterface $to
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
          fclose($fp_in);
          $success = TRUE;
        }
      }
    }

    return $success;
  }

  /**
   * Gzip decode a file.
   *
   * @param \BackupMigrate\Core\File\BackupFileReadableInterface $from
   * @param \BackupMigrate\Core\File\BackupFileWritableInterface $to
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
   * Get the compression options as an options array for a form item.
   *
   * @return array
   */
  protected function _availableCompressionAlgorithms() {
    $compression_options = array("none" => ("No Compression"));
    if (function_exists("gzencode")) {
      $compression_options['gzip'] = ("GZip");
    }
    if (function_exists("bzcompress")) {
      $compression_options['bzip'] = ("BZip");
    }
    if (class_exists('ZipArchive')) {
      $compression_options['zip'] = ("Zip");
    }
    return $compression_options;
  }

  /**
   * Get the default compression algorithm based on those available
   *
   * @return string
   *  The machine name of the algorithm.
   */
  protected function _defaultCompressionAlgorithm() {
    $available = array_keys($this->_availableCompressionAlgorithms());
    // Remove the 'none' option.
    array_shift($available);
    $out = array_shift($available);
    // Return the first available algorithm or 'none' of none other exist.
    return $out ? $out : 'none';
  }

}