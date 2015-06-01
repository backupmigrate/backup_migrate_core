<?php

/**
 * @file
 * Contains \BackupMigrate\Core\Base\ModelBase.
 */

namespace BackupMigrate\Core\Base;


class ModelBase
{
  /**
   * The item id.
   *
   * @var string
   */
  protected $id;

  /**
   * The item name/label.
   *
   * @var string
   */
  protected $name;

  /**
   * Constructor, hydrate from a keyed array. Let the consuming code figure out how to get
   * the relevent data into an array.
   */
  function __construct($params = array()) {
    $this->fromArray($this->_mergeValues((array)$params, (array)$this->getDefaultValues()));
  }

  /**
   * Merge parameters with the given defaults.
   *
   * Works like array_merge_recursive, but it doesn't turn scalar values into arrays.
   */
  private function _mergeValues($params, $defaults) {
    foreach ($defaults as $key => $val) {
      if (!isset($params[$key])) {
        $params[$key] = $val;
      }
      else if (is_array($params[$key])) {
        $params[$key] = $this->_mergeValues($params[$key], $val);
      }
    }
    return $params;
  }

  /**
   * Get the default values for standard parameters.
   */
  protected function getDefaultValues() {
    return array();
  }

  /**
   * Get the default values for standard parameters.
   */
  protected function getSchema() {
    return array();
  }

  /**
   * Load an existing item from an array.
   */
  public function fromArray($params) {
    // @TODO: Only copy values specified in the schema for this model
    foreach ($params as $key => $value) {
      $this->{$key} = $value;
    }
  }

  /**
   * Return as an array of values.
   */
  function toArray() {
    $out = array();

    // Return fields as specified in the schema.
    $schema = $this->getSchema();
    if (!empty($schema['fields']) && is_array($schema['fields'])) {
      foreach ($schema['fields'] as $field => $info) {
        $out[$field] = $this->get($field);
      }
    }
    return $out;
  }

  /**
   * Get the member with the given key.
   */  
  public function __get($key) {
    if (method_exists($this, 'get_'. $key)) {
      return $this->{'get_'. $key}();
    }
    return @$this->{$key};
  }

  /**
   * Set the member with the given key.
   */  
  public function __set($key, $value) {
    if (method_exists($this, 'set_'. $key)) {
      return $this->{'set_'. $key}($value);
    }
    return @$this->{$key};
  }

}
