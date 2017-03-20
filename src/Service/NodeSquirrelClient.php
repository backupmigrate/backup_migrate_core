<?php

namespace BackupMigrate\Core\Service;

use BackupMigrate\Core\Exception\BackupMigrateException;
use BackupMigrate\Core\File\BackupFileReadableInterface;

/**
 * Class NodeSquirrelClient
 * @package BackupMigrate\Core\Service
 */
class NodeSquirrelClient {
  /**
   * @var string
   */
  protected $secret_key = '';

  /**
   * @var HttpClientInterface
   */
  protected $http_client = null;

  /**
   * @var string[]
   */
  protected $api_endpoint = [];

  /**
   * @var []
   */
  protected $crypto_values;

  /**
   * NodeSquirrelClient constructor.
   *
   * @param $secret_key
   * @param array $api_endpoints
   */
  public function __construct(
    $secret_key = null,
    $api_endpoints = ['api.nodesquirrel.com']
  ) {
    $this->secret_key = $secret_key;
    $this->api_endpoint = $api_endpoints;
  }

  /**
   * Get the list of backups from the API
   *
   * @return \array[] An array of assocative arrays of the file info
   */
  public function listFiles() {
    return $this->call('backups.listFiles', array($this->getSiteID()));
  }

  /**
   * Send a readable backup file to NodeSquirrel if the site limits allow it.
   *
   * @param \BackupMigrate\Core\File\BackupFileReadableInterface $file
   * @return \BackupMigrate\Core\File\BackupFileReadableInterface
   * @throws \BackupMigrate\Core\Exception\BackupMigrateException
   */
  public function uploadFile(BackupFileReadableInterface $file) {
    $site_id = $this->getSiteID();
    $filename = $file->getFullName();
    $filesize = $file->getMeta('filesize');

    // Get an upload ticket
    try {
      $ticket = $this->call('backups.getUploadTicket', array($site_id, $filename, $filesize, $file->getMetaAll()));
    } catch (BackupMigrateException $e) {
      throw new BackupMigrateException(
        'Could not initiate an upload to NodeSquirrel. Error: %err (code: %code)',
        array('%err' => $e->getMessage(), '%code' => $e->getCode())
      );
    }

    // Post the file
    try {
      $this->getHttpClient()->postFile($ticket['url'], $file, $ticket['params']);
    } catch (BackupMigrateException $e) {
      throw new BackupMigrateException(
        'Could not upload to NodeSquirrel: %err (code: %code)',
        array('%err' => $e->getMessage(), '%code' => $e->getCode())
      );
    }

    // Confirm the upload.
    try {
      $this->call('backups.confirmUpload', [$site_id, $filename, $filesize]);
    } catch (BackupMigrateException $e) {
      throw new BackupMigrateException(
        'Could not confirm the upload to NodeSquirrel: %err (code: %code)',
        array('%err' => $e->getMessage(), '%code' => $e->getCode())
      );
    }
  }

  /**
   * Send a delete call to the API
   *
   * @param $id
   * @return mixed
   */
  public function deleteFile($id) {
    return $this->call('backups.deleteFile', array($this->getSiteID(), $id));
  }

  /**
   * Call a method on the API
   *
   * @param string $method
   * @param array $args
   * @return mixed
   * @throws \BackupMigrate\Core\Exception\BackupMigrateException
   */
  public function call($method, $args) {
    // Add the key authentication arguments if we can.
    $args = $this->signRequest($args);
    // Call the API using xmlrpc.
    return $this->xmlrpcCall($method, $args, $this->getEndpoints());
  }

  /**
   * Do the actual call. The args must be signed with a secret key already.
   *
   * It may call itself to fetch new endpoint URLS if needed. The retry argument
   * prevents an infinite loop if new endpoints cannot be retrieved.
   *
   * @param $method
   * @param $args
   * @param $endpoints
   * @param int $retry
   * @return mixed
   * @throws \BackupMigrate\Core\Exception\BackupMigrateException
   */
  protected function xmlrpcCall($method, $args, $endpoints, $retry = 3) {
    if ($endpoints && --$retry > 0) {
      $endpoint = reset($endpoints);

      // Try each available server in order.
      while ($endpoint) {
        // Add the protocol to the url
        if (strpos($endpoint, 'http') !== 0) {
          $endpoint = 'https://' . $endpoint;
        }

        // Do the actual call
        try {
          // Encode the request.
          $post = xmlrpc_encode_request($method, $args);
          // Post the request.
          $out = $this->getHttpClient()->post($endpoint, $post);
          // Decode the response.
          $out = xmlrpc_decode($out);

          // Check for xml errors.
          if (isset($out['faultCode'])) {
            throw new BackupMigrateException($out['faultString'], [], $out['faultCode']);
          }

          return $out;
        }
        catch (BackupMigrateException $e) {
          // Deal with errors.
          switch ($e->getCode()) {
            case '500':
            case '503':
            case '404':
              // Some sort of server error. Try the next one.
              $endpoint = next($endpoints);

              // If we're at the end of the line then try refetching the urls
              if (!$endpoint) {
                $endpoints = $this->fetchEndpoints(TRUE, $retry);
                return $this->xmlrpcCall($method, $args, $endpoints, $retry);
              }
              break;
            case '300':
              // 'Multiple Choices' means that the existing server list needs to be refreshed.
              $servers = $this->fetchEndpoints(TRUE, $retry);
              return $this->xmlrpcCall($method, $args, $servers, $retry);
              break;
            case '401':
            case '403':
              // Authentication failed.
              throw new BackupMigrateException('Couldn\'t log in to NodeSquirrel. The server error was: %err',
                array('%err' => $e->getMessage()));
              break;
            default:
              // Some sort of client error. Don't try the next server because it'll probably say the same thing.
              throw new BackupMigrateException('The NodeSquirrel server returned the following error: %err',
                array('%err' => $e->getMessage()));
              break;
          }
        }
      }
    }
  }

  /**
   * @param string $secret_key
   */
  public function setSecretKey($secret_key) {
    $this->secret_key = $secret_key;
  }

  /**
   * Do the actual XMLRPC call
   *
   * @param $endpoint
   * @param $method
   * @param $args
   * @return array
   * @throws \BackupMigrate\Core\Exception\BackupMigrateException
   */
  protected function doXmlrpcCall($endpoint, $method, $args) {
    if (!function_exists('xmlrpc_encode')) {
      throw new BackupMigrateException('NodeSquirrel requires the php XML-RPC extension.');
    }

    // Encode the request.
    $post = xmlrpc_encode_request($method, $args);
    // Post the request.
    $out = $this->getHttpClient()->postData($endpoint, $post);
    // Decode the response.
    $out = xmlrpc_decode($out);
    return $out;
  }

  /**
   * Sign a set of method arguments with our secret key.
   *
   * @param $args
   * @return bool
   */
  protected function signRequest($args) {
    $crypto = $this->getCryptoValues();
    $hash = $this->getHash($crypto['time'], $crypto['nonce']);
    if ($hash) {
      array_unshift($args, $crypto['nonce']);
      array_unshift($args, $crypto['time']);
      array_unshift($args, $hash);
      return $args;
    }
    else {
      return FALSE;
    }
  }

  /**
   * Get a hash to use as a secure 1-time signature for a request.
   *
   * @param $time
   * @param $nonce
   * @return string
   * @throws \BackupMigrate\Core\Exception\BackupMigrateException
   */
  protected function getHash($time, $nonce) {
    if ($private_key = $this->getPrivateKey()) {
      $message = $time . ':' . $nonce . ':' . $private_key;
      // Use HMAC-SHA1 to authenticate the call.
      $hash = base64_encode(
        pack('H*',
          sha1((str_pad($private_key, 64, chr(0x00)) ^ (str_repeat(chr(0x5c),
                64))) .
        pack('H*',
          sha1((str_pad($private_key, 64, chr(0x00)) ^ (str_repeat(chr(0x36),
                64))) .
        $message))))
      );
      return $hash;
    }
    throw new BackupMigrateException('You must enter a valid secret key to use NodeSquirrel.',
      array());
  }

  /**
   * Get the variable inputs to the hash function. This let's us stub this with known values during testing.
   * @return array
   */
  protected function getCryptoValues() {
    if ($this->crypto_values) {
      return $this->crypto_values;
    }
    return [
      'nonce' => md5(mt_rand()),
      'time' => time(),
    ];
  }

  /**
   * Can be used to fix the random/timebased signing values.
   *
   * Should only be used for testing purposes
   * @param $values
   */
  public function setCryptoValues($values) {
    $this->crypto_values = $values;
  }

  /**
   * Retrieve the list of servers by making an rpc call to the servers we know about.
   */
  function refetchEndpoints($refresh = FALSE, $retry = 3) {
    // TODO: Implement this as it needs local caching to be effective.
    return [];
  }

  /**
   * @return \BackupMigrate\Core\Service\HttpClientInterface
   * @throws \BackupMigrate\Core\Exception\BackupMigrateException
   */
  public function getHttpClient() {
    if (!$this->http_client) {
      $this->http_client = new PhpCurlHttpClient();
    }
    return $this->http_client;
  }

  /**
   * @param HttpClientInterface $http_client
   */
  public function setHttpClient(HttpClientInterface $http_client) {
    $this->http_client = $http_client;
  }


  /**
   * Get a list of API endpoint urls (without the protocol)
   *
   * @return mixed
   */
  protected function getEndpoints() {
    return $this->api_endpoint;
  }

  /**
   * Get the secret key.
   *
   * @return string
   */
  protected function getSecretKey() {
    return $this->secret_key;
  }

  /**
   * Get the site id from the secret key
   *
   * @return mixed
   */
  protected function getSiteID() {
    list($id,) = explode(':', $this->getSecretKey());
    return $id;
  }

  /**
   * Get the site id from the secret key
   *
   * @return mixed
   */
  protected function getPrivateKey() {
    list(,$key) = explode(':', $this->getSecretKey());
    return $key;
  }
}
