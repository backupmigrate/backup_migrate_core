<?php
/**
 * @file
 * Contains BackupMigrate\Core\Destination\BrowserDownloadDestination
 */


namespace BackupMigrate\Core\Destination;


use BackupMigrate\Core\Exception\DestinationNotWritableException;
use BackupMigrate\Core\File\BackupFileReadableInterface;
use BackupMigrate\Core\Plugin\PluginCallerInterface;
use BackupMigrate\Core\Plugin\PluginCallerTrait;

/**
 * Class BrowserDownloadDestination
 * @package BackupMigrate\Core\Destination
 */
class BrowserDownloadDestination extends StreamDestination implements WritableDestinationInterface, PluginCallerInterface {
  use PluginCallerTrait;

  /**
   * {@inheritdoc}
   */
  function saveFile(BackupFileReadableInterface $file) {
    // Set some default download headers.
    $headers = array(
      array('key' => 'Content-Disposition', 'value' => 'attachment; filename="'. $file->getFullName() .'"'),
      array('key' => 'Cache-Control', 'value' => 'no-cache'),
    );

    // Set a mime-type header.
    if ($mime = $file->getMeta('mimetype')) {
      $headers[] = array('key' => 'Content-Type', 'value' => $mime);
    }
    else {
      // Get the mime type for this file if possible
      $mime = 'application/octet-stream';
      $mime = $this->plugins()->call('alterMime', $mime, array('ext' => $file->getExtLast()));

      $headers[] = array('key' => 'Content-Type', 'value' => $mime);
    }

    // In some circumstances, web-servers will double compress gzipped files.
    // This may help aleviate that issue by disabling mod-deflate.
    if ($file->getMeta('mimetype') == 'application/x-gzip') {
      if (function_exists('apache_setenv')) {
        apache_setenv('no-gzip', '1');
      }
      $headers[] = array('key' => 'Content-Encoding', 'value' => 'gzip');
    }
    if ($size = $file->getMeta('filesize')) {
      $headers[] = array('key' => 'Content-Length', 'value' => $size);
    }

    // Suppress the warning you get when the buffer is empty.
    @ob_end_clean();

    if ($file->openForRead()) {
      foreach ($headers as $header) {
        // To prevent HTTP header injection, we delete new lines that are
        // not followed by a space or a tab.
        // See http://www.w3.org/Protocols/rfc2616/rfc2616-sec4.html#sec4.2
        $header['value'] = preg_replace('/\r?\n(?!\t| )/', '', $header['value']);
        header($header['key'] . ': ' . $header['value']);
      }
      // Transfer file in 1024 byte chunks to save memory usage.
      while ($data = $file->readBytes(1024 * 512)) {
        print $data;
      }
      $file->close();
    }
    // @TODO Throw exception.
  }

  /**
   * {@inheritdoc}
   */
  public function checkWritable() {
    // Check that we're running as a web process via a browser.
    // @TODO: we could check if the 'HTTP_ACCEPT' header contains the right mime but that is probably overkill.
    if (!isset($_SERVER['REQUEST_METHOD'])) {
      throw new DestinationNotWritableException(
        "The download destination only works when accessed through a http client."
      );
    }
  }


}
