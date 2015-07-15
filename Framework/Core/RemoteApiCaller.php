<?php
/**
 * Cntysoft Cloud Software Team
 */
namespace Cntysoft\Framework\Core;
use Cntysoft\Kernel;
use Zend\Http\Client;
 /**
 * @author SOFTBOY <cntysoft@163.com>
 * @copyright  Copyright (c) 2010-2011 Cntysoft Technologies China Inc. <http://www.cntysoft.com>
 * @license    http://www.cntysoft.com/license/new-bsd     New BSD License
 */
class RemoteApiCaller
{
   /**
    * API服务器的调用地址
    *
    * @var string $server
    */
   protected $server;
   /**
    * @var string $token
    */
   protected $token;
   /**
    * @var \Zend\Http\Client $client
    */
   protected $client;
   public function __construct($server, $token)
   {
      $this->server = $server;
      $crypter = new \Phalcon\Crypt();
      $token = md5($token);
      $this->token = $crypter->encrypt($token, $token);
      $this->client = new Client($this->server);
      $this->client->setMethod('POST');
   }

   public function call($cls, $method, array $params = array())
   {
      $this->client->setParameterPost(array(
         \Cntysoft\INVOKE_META_KEY => json_encode(array(
            'cls' => $cls,
            'method' => $method
         )),
         \Cntysoft\INVOKE_PARAM_KEY => json_encode($params),
         \Cntysoft\INVOKE_SECURITY_KEY => $this->token
      ));

      $response = $this->client->send();
      return json_decode($response->getBody(), true);
   }
}