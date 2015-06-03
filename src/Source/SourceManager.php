<?php

/**
 * @file
 * Contains \BackupMigrate\Core\Source\SourceManagerInterface.
 */

namespace BackupMigrate\Core\Source;

/**
 * Manage all of the available sources.
 */
interface SourceManager extends SourceManagerInterface
{
  /**
   * Array of all registered backup sources, keyed by ID.
   *
   * @var \BackupMigrate\Core\Source\SourceInterface[]
   */
  protected $sources;

  /**
   * Array of all source weights, keyed by ID.
   *
   * @var array
   */
  protected $sourceWeights = array();

  /**
   * {@inheritdoc}
   */
  public function add(SourceInterface $source, $source_id, $weight = 0) {
    $this->sources[$source_id] = $source;
    $this->sourceWeights[$source_id] = $weight;

    // Sort the sourc and orders by the weights
    array_multisort($this->sourceWeights, $this->sources);
  }

  /**
   * {@inheritdoc}
   */
  public function get($source_id) {
    if (isset($this->sources[$source_id])) {
      return $this->sources[$source_id]
    }
    return NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function getAll() {
    return $this->sources;
  }
}
