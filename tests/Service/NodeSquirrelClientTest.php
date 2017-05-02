<?php


namespace BackupMigrate\Core\Service;

use BackupMigrate\Core\Tests\File\TempFileConsumerTestTrait;


/**
 * Class NodeSquirrelClientTest
 * @package BackupMigrate\Core\Service
 */
class NodeSquirrelClientTest extends \PHPUnit_Framework_TestCase {
  use TempFileConsumerTestTrait;

  /**
   * @var HttpClientInterface
   */
  protected $http_client;
  /**
   * @var NodeSquirrelClient
   */
  protected $nodesquirrel_client;
  /**
   * @var string
   */
  protected $secret_key;

  /**
   * @var string
   */
  protected $site_id;
  protected $time;
  protected $nonce;

  /**
   *
   */
  public function setUp() {
    parent::setUp();

    $this->_setUpFiles([
      'tmp' => [],
    ]);


    $private_key = 'b0643b8f939cfcc390efc2fb55385b1a';
    $this->site_id = "db5997406d336ef9583812c9f9370b8d";
    $this->secret_key = "$this->site_id:$private_key";
    $this->http_client = $this->getMock(HttpClientInterface::class);

    $this->nodesquirrel_client = new NodeSquirrelClient($this->secret_key, ['api.nodesquirrel.com']);

    $this->time = 1489959884;
    $this->nonce = '5ff9e8ae24b6432bec5c3875c753414f';
    $this->nodesquirrel_client->setCryptoValues([
      'time' => $this->time,
      'nonce' => $this->nonce,
    ]);

    // Recreate the hash that NS expects. There's probably a better way to test this.
    $message = "$this->time:$this->nonce:$private_key";
    // Use HMAC-SHA1 to authenticate the call.
    $this->hash = base64_encode(
      pack('H*',
        sha1((str_pad($private_key, 64, chr(0x00)) ^ (str_repeat(chr(0x5c),
              64))) .
          pack('H*',
            sha1((str_pad($private_key, 64, chr(0x00)) ^ (str_repeat(chr(0x36),
                  64))) .
              $message))))
    );

    $this->nodesquirrel_client->setHttpClient($this->http_client);
  }

  public function testCallAPI() {
    $fn = 'testcall';
    $args = ['foo', 'bar', 'baz'];

    $xml_request = '<?xml version="1.0" encoding="iso-8859-1"?>
<methodCall>
<methodName>testcall</methodName>
<params>
 <param>
  <value>
   <string>' . $this->hash . '</string>
  </value>
 </param>
 <param>
  <value>
   <int>' . $this->time . '</int>
  </value>
 </param>
 <param>
  <value>
   <string>' . $this->nonce . '</string>
  </value>
 </param>
 <param>
  <value>
   <string>foo</string>
  </value>
 </param>
 <param>
  <value>
   <string>bar</string>
  </value>
 </param>
 <param>
  <value>
   <string>baz</string>
  </value>
 </param>
</params>
</methodCall>
';


    $this->http_client
      ->expects($this->once())
      ->method('post')
      ->with('https://api.nodesquirrel.com', $xml_request)
      ->willReturn(xmlrpc_encode('Ok'));

    $actual = $this->nodesquirrel_client->call($fn, $args);
    $this->assertEquals('Ok', $actual);

    // TODO: Test endpoint failover and list refresh
  }

  public function testUploadFile() {
    $file = $this->manager->create('txt');
    $txt = 'Hello, World 4!';
    $file->writeAll($txt);
    $file->setName('item4');
    $meta = ['foo' => 'bar', 'filesize' => strlen($txt), 'datestamp' => time()];
    $file->setMetaMultiple($meta);

    // Get ticket
    $this->http_client
      ->expects($this->at(0))
      ->method('post')
      ->with('https://api.nodesquirrel.com',
        xmlrpc_encode_request('backups.getUploadTicket', [
          $this->hash,
          $this->time,
          $this->nonce,
          $this->site_id,
          'item4.txt',
          strlen($txt),
          ['filesize' => strlen($txt), 'datestamp' => time(), 'foo' => 'bar']
      ]))
      ->willReturn(xmlrpc_encode([
        'url' => 'https://files.nodesquirrel.com/post',
        'params' => ['foo' => 'bar']
      ]));

    // Post file
    $this->http_client
      ->expects($this->at(1))
      ->method('postFile')
      ->with('https://files.nodesquirrel.com/post', $file, ['foo' => 'bar'])
      ->willReturn('Ok');

    // Confirm upload
    $this->http_client
      ->expects($this->at(2))
      ->method('post')
      ->with('https://api.nodesquirrel.com',
        xmlrpc_encode_request('backups.confirmUpload', [
          $this->hash,
          $this->time,
          $this->nonce,
          $this->site_id,
          'item4.txt',
          strlen($txt),
        ]))
      ->willReturn(xmlrpc_encode([
        'url' => 'https://files.nodesquirrel.com/post',
        'params' => ['foo' => 'bar']
      ]));

    $this->nodesquirrel_client->uploadFile($file);

    // TODO: Test exception handling.
  }
}
