<?php
/**
 * @file
 * Contains BackupMigrate\Core\Destination\DebugDestination
 */


namespace BackupMigrate\Core\Destination;


use BackupMigrate\Core\Config\Config;
use BackupMigrate\Core\File\BackupFileReadableInterface;
use BackupMigrate\Core\Plugin\PluginCallerInterface;

/**
 * Class DebugDestination
 * @package BackupMigrate\Core\Destination
 */
class DebugDestination extends StreamDestination implements WritableDestinationInterface {

  /**
   * {@inheritdoc}
   */
  function saveFile(BackupFileReadableInterface $file) {
    $out = array();

    // Quick and dirty way to html format this output
    if ($this->confGet('format') == 'html') {
      print '<pre>';
    }

    // Output the metadata
    if ($this->confGet('showmeta')) {
      print "---------------------\n";
      print "Metadata: \n";
      print_r($file->getMetaAll());
      print "---------------------\n";
    }

    // Output the body
    if ($this->confGet('showbody')) {
      print "---------------------\n";
      print "Body: \n";

      $max = $this->confGet('maxbody');
      $chunk = min($max, 1024);
      if ($file->openForRead()) {
        // Transfer file in 1024 byte chunks to save memory usage.
        while ($max > 0 && $data = $file->readBytes($chunk)) {
          print $data;
          $max -= $chunk;
        }
        $file->close();
      }
      print "---------------------\n";
    }


    // Quick and dirty way to html format this output
    if ($this->confGet('format') == 'html') {
      print '</pre>';
    }

    exit;
  }

  /**
   * Get the default values for the plugin.
   *
   * @return \BackupMigrate\Core\Config\Config
   */
  public function configDefaults() {
    return new Config([
      'showmeta' => true,
      'showbody' => true,
      'maxbody' => 1024 * 16,
      'format' => 'text',
    ]);
  }
}
