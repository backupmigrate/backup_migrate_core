<?php
/**
 * @file
 * Contains BackupMigrate\Core\Source\FileDirectorySource
 */


namespace BackupMigrate\Core\Source;


use Archive_Tar;
use BackupMigrate\Core\Exception\BackupMigrateException;
use BackupMigrate\Core\Exception\IgnorableException;
use BackupMigrate\Core\Plugin\FileProcessorInterface;
use BackupMigrate\Core\Plugin\FileProcessorTrait;
use BackupMigrate\Core\Plugin\PluginBase;
use BackupMigrate\Core\File\BackupFileReadableInterface;

/**
 * Class FileDirectorySource
 * @package BackupMigrate\Core\Source
 */
class FileDirectorySource extends PluginBase
  implements SourceInterface, FileProcessorInterface
{
  use FileProcessorTrait;

  /**
   * {@inheritdoc}
   */
  public function supportedOps() {
    return [
      'exportToFile' => [],
      'importFromFile' => []
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function exportToFile() {
    if ($directory = $this->confGet('directory')) {
      $file = $this->getTempFileManager()->create('tar');

      if ($files = $this->getFilesToBackup($directory)) {
        $tar = new Archive_Tar($file->realpath());
        $tar->addModify($files, '', $directory);
        return $file;
      }
      throw new BackupMigrateException('The directory %dir does not not have any files to be backed up.',
        array('%dir' => $directory));
    }
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function importFromFile(BackupFileReadableInterface $file) {
    // @TODO: Implement this.
  }

  /**
   * Get a list if files to be backed up from the given directory. Do not
   * include files that match the 'exclude_filepaths' setting.
   *
   * @param string $dir The name of the directory to list.
   * @return array
   * @throws \BackupMigrate\Core\Exception\BackupMigrateException
   * @throws \BackupMigrate\Core\Exception\IgnorableException
   * @internal param $directory
   */
  protected function getFilesToBackup($dir) {
    $exclude = $this->confGet('exclude_filepaths');

    if (!file_exists($dir)) {
      throw new BackupMigrateException('Directory %dir does not exist.',
        array('%dir' => $dir));
    }
    if (!is_dir($dir)) {
      throw new BackupMigrateException('The file %dir is not a directory.',
        array('%dir' => $dir));
    }
    if (!is_readable($dir)) {
      throw new BackupMigrateException('Directory %dir could not be read from.',
        array('%dir' => $dir));
    }

    // Get a filtered list if files from the directory.
    list($out, $errors) = $this->_getFilesFromDirectory($dir, $exclude);

    // Alert the user to any errors there might have been.
    if ($errors) {
      $count = count($errors);
      $file_list = implode(', ', array_slice($errors, 0, 5));
      if ($count > 5) {
        $file_list .= ', ...';
      }

      if (!$this->confGet('ignore_errors')) {
        throw new IgnorableException('The backup could not be completed because !count files could not be read: (!files).',
          array('!count' => $count, '!files' => $file_list));
      }
      else {
        // throw new IgnorableException('!count files could not be read: (!files).', array('!files' => $filesmsg));
        // @TODO: Log the ignored files.
      }
    }

    return $out;
  }

  /**
   * @param string $dir The name of the directory to list.
   * @param array $exclude An array of exclude rules.
   * @return array
   */
  protected function _getFilesFromDirectory($dir, $exclude = array()) {
    $out = $errors = array();

    // Open the directory.
    if (!$handle = opendir($dir)) {
      $errors[] = $dir;
    }
    else {
      while (($file = readdir($handle)) !== FALSE) {
        // If not a dot file and the file name isn't excluded.
        if ($file != '.' && $file != '..' && !in_array($file, $exclude)) {
          $real = realpath($dir . '/' . $file);

          // If the full path is not excluded.
          if (!in_array($real, $exclude)) {
            if (is_dir($real)) {
              list($sub_files, $sub_errors) =
                  $this->_getFilesFromDirectory($real, $exclude);

              // If the directory is empty, add an empty directory.
              if (count($sub_files) == 0) {
                $out[] = $real;
              }
              $out = array_merge($out, $sub_files);
              $errors = array_merge($errors, $sub_errors);
            }
            else {
              if (is_readable($real)) {
                $out[] = $real;
              }
              else {
                $errors[] = $dir . '/' . $file;
              }
            }
          }
        }
      }
      closedir($handle);
    }

    return array($out, $errors);
  }
}