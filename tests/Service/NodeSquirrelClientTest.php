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

  /**
   *
   */
  public function setUp() {
    parent::setUp();

    $this->_setUpFiles([
      'tmp' => [],
    ]);


    $this->secret_key = "db5997406d336ef9583812c9f9370b8d:b0643b8f939cfcc390efc2fb55385b1a";
    $this->site_id = "db5997406d336ef9583812c9f9370b8d";
    $this->http_client = $this->getMock(HttpClientInterface::class);

    $this->nodesquirrel_client = new NodeSquirrelClient($this->secret_key, ['api.nodesquirrel.com']);
    $this->nodesquirrel_client->setCryptoValues([
      'time' => 1489959884,
      'nonce' => '5ff9e8ae24b6432bec5c3875c753414f',
    ]);
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
   <string>ItrNr8d+KgCXU6LSq2P1ngnn8Is=</string>
  </value>
 </param>
 <param>
  <value>
   <int>1489959884</int>
  </value>
 </param>
 <param>
  <value>
   <string>5ff9e8ae24b6432bec5c3875c753414f</string>
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
          'ItrNr8d+KgCXU6LSq2P1ngnn8Is=',
          1489959884,
          '5ff9e8ae24b6432bec5c3875c753414f',
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
          'ItrNr8d+KgCXU6LSq2P1ngnn8Is=',
          1489959884,
          '5ff9e8ae24b6432bec5c3875c753414f',
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
