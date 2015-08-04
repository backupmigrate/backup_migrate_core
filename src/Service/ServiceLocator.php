<?php
/**
 * @file
 * Contains BackupMigrate\Core\Service\ServiceLocator
 */


namespace BackupMigrate\Core\Service;


/**
 * A very simple service locator. Does not handle dependency injection but could
 * be replaced by a more complex application specific version which does.
 *
 * Class ServiceLocator
 * @package BackupMigrate\Core\Service
 */
class ServiceLocator implements ServiceLocatorInterface {

  /**
   * @var array
   */
  protected $services;


  /**
   * The constructor. Initialise the list of services.
   */
  function __construct() {
    $this->services = [];
  }

  /**
   * Add a fully configured service to the service locator.
   *
   * @param string $type
   *  The service type identifier.
   * @param mixed $service
   * @return null
   */
  public function add($type, $service) {
    $this->services[$type] = $service;
  }

  /**
   * Retrieve a service from the locator
   *
   * @param string $type
   *  The service type identifier
   * @return mixed
   */
  public function get($type) {
    return $this->services[$type];
  }

  /**
   * Get an array of keys for all available services.
   *
   * @return array
   */
  public function keys() {
    return array_keys($this->services);
  }
}