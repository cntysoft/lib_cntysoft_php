<?php
/**
 * Cntysoft Cloud Software Team
 *
 * @author ZhiHui <liuyan2526@qq.com>
 * @copyright  Copyright (c) 2010-2011 Cntysoft Technologies China Inc. <http://www.cntysoft.com>
 * @license    http://www.cntysoft.com/license/new-bsd     New BSD License
 */
namespace Cntysoft\Framework\Cloud\WeChat;

use Cntysoft\Kernel;
use App\Shop\Setting\Model\SiteBaseInfo as BaseInfoModel;
use Zend\Http\Client as HttpClient;
use Zend\Http\Request as HttpRequest;
use Zend\Http\Client\Adapter\Curl as CurlAdapter;

/**
 * 此类用来封装微信公众号接口
 * 
 */
class Sdk
{
   const CACHER_ACCESS_TOKEN = 'wechataccesstoken';
   
   const CACHER_CODE_ACCESS_TOKEN = 'wechatcodeaccesstoken';
   /**
    * @var string $appId 
    */
   protected $appId = null;
   /**
    * @var string $appSecret
    */
   protected $appSecret = null;
   /**
    * @var Zend\Http\Client $client
    */
   protected $client = null;
   /**
    * @var Zend\Http\Client\Adapter\Curl $adapter 
    */
   protected $adapter = null;
   /**
    * @var Kernel\make_cache_object $cacher 
    */
   protected $cacher = null;

   public function __construct(array $config)
   {
      if(!array_key_exists('appId', $config) || !array_key_exists('appSecret', $config)){
         $errorType = ErrorType::getInstance();
         Kernel\throw_exception(new Exception(
                 $errorType->msg('E_CONSTRUCT_PARAMS_ERROR'), $errorType->code('E_CONSTRUCT_PARAMS_ERROR')
                 ), $errorType);
      }
      $this->appId = $config['appId'];
      $this->appSecret = $config['appSecret'];
   }
   /**
    * 存储access_token
    */
   public function saveAccessToken()
   {
      $ret = $this->curlHttp(Constant::WECHAT_GET_ACCESSTOKEN_URL, false, array(
         'grant_type' => 'client_credential',
         'appid' => $this->appId,
         'secret' => $this->appSecret
      ));
      $cacher = $this->getCacher();
      $cacher->save(self::CACHER_ACCESS_TOKEN,$ret['access_token'],$ret['expires_in']);
   }
   /**
    * 获取存储的access_token
    * 
    */
   public function getAccessToken()
   {
      $cacher = $this->getCacher();
      $accesstoken = $cacher->get(self::CACHER_ACCESS_TOKEN);
      if(!$accesstoken){
         $this->saveAccessToken();
         $accesstoken = $cacher->get(self::CACHER_ACCESS_TOKEN);
      }
      return $accesstoken;
   }
   
   public function getAccessTokenByCode($code)
   {
      $ret = $this->curlHttp(Constant::WECHAT_GET_ACCESS_TOKEN_BY_CODE, false, array(
         'appid' => $this->appId,
         'secret' => $this->appSecret,
         'code' => $code,
         'grant_type' => 'authorization_code'
      ));
      return $ret;
   }
   /**
    * 获取微信服务器的ip地址列表
    * 
    * @return type
    */
   public function getWechatIpList()
   {
      $ret = $this->curlHttp(Constant::WECHAT_GET_IP_LIST, false, array(
         'access_token' => $this->getAccessToken()
      ));
      return $ret['ip_list'];
   }
   /**
    * 创建用户分组
    * 
    * @param type $name
    */
   public function createUserGroups($name)
   {
      $params = array(
         'group' => array(
            'name' => $name
         )
      );
      $url = Constant::WECHAT_CREATE_USER_GROUPS.'?access_token='.$this->getAccessToken();
      $ret = $this->curlHttp($url, true, $params);
      var_dump($ret);
   }
   /**
    * 获取全部的用户分组
    * 
    * @return type
    */
   public function getAllUserGroups()
   {
      $ret = $this->curlHttp(Constant::WECHAT_GET_USER_GROUPS_LIST, false, array(
         'access_token' => $this->getAccessToken()
      ));
      return $ret;
   }
   /**
    * 获取指定用户所在的分组ID
    * 
    * @param type $openId
    * @return type
    */
   public function getGroupByUserOpenId($openId)
   {
      $params = array(
         'openid' => $openId
      );
      $url = Constant::WECHAT_GET_GROUP_BY_USER_OPENID.'?access_token='.$this->getAccessToken();
      $ret = $this->curlHttp($url, true, $params);
      return $ret;
   }
   /**
    * 修改指定的分组名称
    * 
    * @param type $groupId
    * @param type $name
    * @return type
    */
   public function modifyGroupName($groupId,$name)
   {
      $params = array(
         'group' => array(
            'id' => $groupId,
            'name' => $name
         )
      );
      $url = Constant::WECHAT_MODIFY_GROUP_NAME.'?access_token='.$this->getAccessToken();
      $ret = $this->curlHttp($url, true, $params);
      if(0 != $ret['errcode']){
         $errorType = ErrorType::getInstance();
         Kernel\throw_exception(new Exception(
            $errorType->msg($ret['errmsg']),
            $errorType->code($ret['errcode'])
         ));
      }
      return $ret;
   }
   /**
    * 移动单个用户到指定的分组
    * 
    * @param type $openId
    * @param type $groupId
    * @return type
    */
   public function moveSingleUserToGroup($openId,$groupId)
   {
      $params = array(
         'openid' => $openId,
         'to_groupid' => $groupId
      );
      $url = Constant::WECHAT_MOVE_USER_TO_GROUP.'?access_token='.$this->getAccessToken();
      $ret = $this->curlHttp($url, true, $params);
      if(0 != $ret['errcode']){
         $errorType = ErrorType::getInstance();
         Kernel\throw_exception(new Exception(
            $errorType->msg($ret['errmsg']),
            $errorType->code($ret['errcode'])
         ));
      }
      return $ret;
   }
   /**
    * 移动多个用户到指定的分组
    * 
    * @param array $openIds
    * @param type $groupId
    * @return type
    */
   public function moveMulitUserToGroup(array $openIds,$groupId)
   {
      $params = array(
         'openid_list' => $openIds,
         'to_groupid' => $groupId
      );
      $url = Constant::WECHAT_MOVE_MULTI_USER_TO_GROUP.'?access_token='.$this->getAccessToken();
      $ret = $this->curlHttp($url, true, $params);
      if(0 != $ret['errcode']){
         $errorType = ErrorType::getInstance();
         Kernel\throw_exception(new Exception(
            $errorType->msg($ret['errmsg']),
            $errorType->code($ret['errcode'])
         ));
      }
      return $ret;
   }
   /**
    * 删除用户分组
    * 
    * @param type $groupId
    * @return type
    */
   public function deleteUserGroup($groupId)
   {
      $params = array(
         'group' => array(
            'id' => $groupId
         )
      );
      $url = Constant::WECHAT_DELETE_GROUP.'?access_token='.$this->getAccessToken();
      $ret = $this->curlHttp($url, true, $params);
      if(0 != $ret['errcode']){
         $errorType = ErrorType::getInstance();
         Kernel\throw_exception(new Exception(
            $errorType->msg($ret['errmsg']),
            $errorType->code($ret['errcode'])
         ));
      }
      return $ret;
   }
   /**
    * 创建自定义菜单
    * 
    * @param array $menu
    * @return type
    */
   public function createMenu(array $menu)
   {
      $url = Constant::WECHAT_MENU_CREATE.'?access_token='.$this->getAccessToken();
      $ret = $this->curlHttp($url, true, $menu);
      if(0 != $ret['errcode']){
         $errorType = ErrorType::getInstance();
         Kernel\throw_exception(new Exception(
            $errorType->msg($ret['errmsg']),
            $errorType->code($ret['errcode'])
         ));
      }
      return $ret;
   }
   /**
    * 获取菜单
    * 
    * @return type
    */
   public function getMenu()
   {
      $params = array(
         'access_token' => $this->getAccessToken()
      );
      $ret = $this->curlHttp(Constant::WECHAT_MENU_GET, false, $params);
      return $ret;
   }
   /**
    * 删除自定义菜单
    * 
    * @return type
    */
   public function deleteMenu()
   {
      $params = array(
         'access_token' => $this->getAccessToken()
      );
      $ret = $this->curlHttp(Constant::WECHAT_MENU_DELETE, false, $params);
      if(0 != $ret['errcode']){
         $errorType = ErrorType::getInstance();
         Kernel\throw_exception(new Exception(
            $errorType->msg($ret['errmsg']),
            $errorType->code($ret['errcode'])
         ));
      }
      return $ret;
   }
   /**
    * 获取自定义菜单配置信息
    * 
    * @return type
    */
   public function getMenuConfig()
   {
      $params = array(
         'access_token' => $this->getAccessToken()
      );
      $ret = $this->curlHttp(Constant::WECHAT_MENU_GET_CONFIG, false, $params);
      return $ret;
   }
   /**
    * 从$nextOpenId开始获取10000个用户列表
    * 
    * @param type $nextOpenId
    * @return type
    */
   public function getUserList($nextOpenId = null)
   {
      $params = array(
         'access_token' => $this->getAccessToken(),
      );
      if($nextOpenId){
         $params['next_openid'] = $nextOpenId;
      };
      $ret = $this->curlHttp(COnstant::WECHAT_GET_USERS_LIST, false,$params);
      return $ret;
   }
   /**
    * 根据用户的openId获取用户的详细信息
    * 
    * @param type $openId
    * @param type $lang
    * @return type
    */
   public function getUsreDetail($openId,$lang = Constant::LANG_TEXT_ZH_CN)
   {
      $params = array(
         'access_token' => $this->getAccessToken(),
         'openid' => $openId,
         'lang' => $lang
      );
      $ret = $this->curlHttp(Constant::WECHAT_GET_USER_DETAIL, false, $params);
      return $ret;
   }
   
   public function getWebUserInfo($accesstoken,$openid,$lang = 'zh_CN')
   {
      $ret = $this->curlHttp(Constant::WECHA_WEB_GET_USER_INFO, false, array(
         'access_token' => $accesstoken,
         'openid' => $openid,
         'lang' => $lang
      ));
      return $ret;
   }
   /**
    * 设置用户的备注名称
    * 
    * @param type $openid
    * @param type $remark
    * @return type
    */
   public function setUserRemarkName($openid,$remark)
   {
      $paramsArr = array(
         'openid' => $openid,
         'remark' => $remark
      );
      $params = json_encode($paramsArr);
      $url = Constant::WECAHT_SET_USER_REMARK.'?access_token='.  $this->getAccessToken();
      $ret = $this->curlHttp($url, true, $paramsArr);
      if(0 != $ret['errcode']){
         $errorType = ErrorType::getInstance();
         Kernel\throw_exception(new Exception(
            $errorType->msg($ret['errmsg']),
            $errorType->code($ret['errcode'])
         ));
      }
      return $ret;
   }
   /**
    * curl方式的HTTP请求方法
    * 
    * @param type $url
    * @param type $isPost
    * @param array $params
    * @return type
    */
   public function curlHttp($url,$isPost,array $params)
   {
      $client = $this->getHttpClient();
      $request = new HttpRequest();
      $adapter = $this->getCurlAdapter();
      $client->setAdapter($adapter);
      $option = array(
         'curloptions' => array(
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_HEADER         => 0
         )
      );
      if ($isPost) {
         $params = json_encode($params);
         $option['curloptions'][CURLOPT_POSTFIELDS] = $params;
         $adapter->setOptions($option);
         $request->setMethod('POST');
         $request->setUri($url);
      } else {
         $adapter->setOptions($option);
         $request->setMethod('GET');
         $request->setUri($url . '?' . http_build_query($params));
      }
      $response = $client->send($request);
      if(200 !== $response->getStatusCode()){
         $errorType = ErrorType::getInstance();
         Kernel\throw_exception(new Exception(
            $errorType->msg('E_REQUEST_ERROR'),
            $errorType->code('E_REQUEST_ERROR')
         ), $errorType);
      }
      $ret = json_decode($response->getBody(), true);
      return $ret;
   }
   
   public function getHttpClient()
   {
      if(null == $this->client){
         $this->client = new HttpClient();
      }
      return $this->client;
   }
   public function getCurlAdapter()
   {
      if(null == $this->adapter){
         $this->adapter = new CurlAdapter();
      }
      return $this->adapter;
   }
   
   public function getCacher()
   {
      if(null == $this->cacher){
         $this->cacher = Kernel\make_cache_object(implode(DS, array('Framework', 'Cloud', 'WeChat' ,'AccessToken')), 7000);
      }
      return $this->cacher;
   }
}