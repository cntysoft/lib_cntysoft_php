<?php
/**
 * Cntysoft Cloud Software Team
 *
 * @author SOFTBOY <cntysoft@163.com>
 * @copyright  Copyright (c) 2010-2011 Cntysoft Technologies China Inc. <http://www.cntysoft.com>
 * @license    http://www.cntysoft.com/license/new-bsd     New BSD License
 */
namespace Cntysoft\Framework\Net\ChangYan;
use Zend\Http\Client as HttpClient;
use Zend\Http\Request as HttpRequest;
use Cntysoft\Kernel;
use Cntysoft\Kernel\ConfigProxy;
use Zend\Stdlib\Parameters;
/**
 * 畅言sdk封装
 */
class Sdk
{
   const CACHE_KEY = 'ChangYanCacheKey';
   /**
    * @var \Zend\Http\Client $client
    */
   protected $client = null;
   /**
    * @var string $appId
    */
   protected $appId = null;
   /**
    * @var string $appSecret
    */
   protected $appSecret = null;
   /**
    * @var array $apiErrorMap
    */
   protected $apiErrorMap = null;

   /**
    * @var \Phalcon\Cache\Backend\File
    */
   protected $cacher = null;
   public function constructor()
   {
      if(null == $this->appId || null == $this->appSecret){
         $meta = self::getAppIdAndAppKey();
         $this->appId = $meta->appid;
         $this->appSecret = $meta->appkey;
      }
   }
   public function  retrieveAccessToken($code)
   {
      $ret = $this->requestApiUrl(Constant::ACCESS_TOKEN_POINT, true, array(
         'client_id' => $this->appId,
         'client_secret' => $this->appSecret,
         'grant_type' => 'authorization_code',
         'code' => $code,
         'redirect_uri' => 'http://changyan.gongzuoyi.net/ChangYan/Callback'
      ));
      if(isset($ret['error_msg'])){
         $errorType = ErrorType::getInstance();
         Kernel\throw_exception(new Exception(
            $errorType->msg('E_API_INVOKE_ERROR', $ret['error_msg']),
            $errorType->code('E_API_INVOKE_ERROR')
         ));
      }
      $cacher = $this->getCacher();
      $cacher->save(self::CACHE_KEY, $ret['access_token'], $ret['expires_in']);
   }

   /**
    * 页面首次加载调用，用于生成文章并返回首页评论列表
    *
    * @param string $topicUrl 文章的URL，如果topic_source_id为空，则为文章唯一标识。同时作为审核后台，链接到原文章页面的URL地址
    * @param string $topicTitle 文章的标题，同时作为审核后台链接到原文章页面的锚点
    * @param string $topicSourceId 文章在本网站的id。如不为空，则为文章唯一标识，即相同的topic_source_id 返回的评论列表相同
    * @param string $topicCategoryId 文章分类/频道
    * @param int $pageSize 首页展示最新列表的条数，默认0
    * @param int $hotSize 最热列表展示条数，默认0
    * @return mixed
    */
   public function loadTopic($topicUrl, $topicTitle = '', $topicSourceId = '', $topicCategoryId = '', $pageSize = 0, $hotSize = 0)
   {
      $ret = $this->requestApiUrl('2/topic/load', false, array(
         'client_id' => $this->appId,
         'topic_url' => $topicUrl,
         'topic_title' => $topicTitle,
         'topic_source_id' => $topicSourceId,
         'topic_category_id' => $topicCategoryId,
         'page_size' => $pageSize,
         'hot_size' => $hotSize
      ));
      //这里可能需要处理出错的情况
      return $ret;
   }

   /**
    * 获取评论列表
    *
    * @param int $topicId 畅言文章的id，通过loadTopic接口获取
    * @param int $pageSize 每页展示条数，默认30
    * @param int $pageNo 当前页数，默认1
    * @return array
    */
   public function getTopicComments($topicId, $pageSize = 30, $pageNo = 1)
   {
      $ret = $this->requestApiUrl('2/topic/comments', false, array(
         'client_id' => $this->appId,
         'topic_id' => $topicId,
         'page_size' => $pageSize,
         'page_no' => $pageNo
      ));
      //这里可能需要处理出错的情况
      return $ret;
   }

   /**
    * 发表评论
    *
    * @param int $topicId
    * @param string $content
    * @param int $replyId
    * @return array
    */
   public function submitComment($topicId, $content, $replyId)
   {
      $ret = $this->requestApiUrl('2/comment/submit', true, array(
         'client_id' => $this->appId,
         'topic_id' => $topicId,
         'content' => $content,
         'reply_id' => $replyId,
         'access_token' => $this->getAccessToken()
      ));
      return $ret;
   }

   /**
    * 获取当前用户信息接口
    *
    * @return array
    */
   public function getUserInfo()
   {
      $ret = $this->requestApiUrl('2/user/info', false, array(
         'client_id' => $this->appId,
         'access_token' => $this->getAccessToken()
      ));
      return $ret;
   }

   /**
    * 批量获取文章评论数接口
    *
    * @param int $topicId
    * @param int $topicSourceId
    * @param string $topicUrl
    * @return array
    */
   public function getTopicCount($topicId, $topicSourceId, $topicUrl)
   {
      $ret = $this->requestApiUrl('2/topic/count', false, array(
         'client_id' => $this->appId,
         'topic_id' => $topicId,
         'topic_source_id' => $topicSourceId,
         'topic_url' => $topicUrl
      ));
      return $ret;
   }

   /**
    * 评论顶踩的接口
    *
    * @param int $topicId
    * @param int $commentId
    * @param $actionType
    * @return array
    */
   public function commentAction($topicId, $commentId, $actionType)
   {
      $ret = $this->requestApiUrl('2/comment/action', false, array(
         'client_id' => $this->appId,
         'topic_id' => $topicId,
         'comment_id' => $commentId,
         'action_type' => $actionType,
         'access_token' => $this->getAccessToken()
      ));
      return $ret;
   }

   /**
    * @return string
    */
   public function getAccessToken()
   {
      $cacher = $this->getCacher();
      $accessToken = $cacher->get(self::CACHE_KEY);
      if(!$accessToken){
         $errorType = ErrorType::getInstance();
         Kernel\throw_exception(new Exception(
            $errorType->msg('E_ACCESS_TOKEN_EXPIRED'),
            $errorType->code('E_ACCESS_TOKEN_EXPIRED')
         ));
      }
      return $accessToken;
   }

   protected function requestApiUrl($url, $isPost, array $params = array())
   {
      $client = $this->getHttpClient();
      $request = new HttpRequest();
      if($isPost){
         if(!empty($params)){
            $request->setPost(new Parameters($params));
         }
         $request->setMethod('POST');
         $client->setEncType(HttpClient::ENC_URLENCODED);
      }else{
         $request->setMethod('GET');
         $url.= '?'.http_build_query($params);
      }
      $url = Constant::API_ENTRY.'/'.$url;
      $request->setUri($url);
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

   public static function openAuthorizerPage()
   {
      $meta = self::getAppIdAndAppKey();
      $url = Constant::API_ENTRY.'/'.Constant::AUTHORIZE_POINT.'?'.http_build_query(array(
            'client_id' => $meta->appid,
            'redirect_uri' => 'http://changyan.gongzuoyi.net/ChangYan/Callback',
            'response_type' => 'code',
            'display' => Constant::DISPLAY_T_WEB,
            'platform_id' => 0
         ));
      echo <<<PAGE
<script language = "javascript">

var winWidth = 600;
var winHeight = 210;
var topPos = (window.screen.height-30-winHeight)/2;
var leftPost = (window.screen.width-10-winWidth)/2;
window.open('$url','畅言认证',
'height='+winHeight+',width='+winWidth+',top='+topPos+',left='+leftPost+',toolbar=no,menubar=no,scrollbars=no,resizable=no,location=no, status=no')
</script>
PAGE;
   }

   /**
    * 关闭当前的认证页面
    */
   public static function closeAuthorizerPage()
   {
      echo <<<PAGE
<script language = "javascript">
window.close();
</script>
PAGE;
   }

   /**
    * 从配置文件获取相关信息
    *
    * @return \Phalcon\Config
    */
   protected static function getAppIdAndAppKey()
   {
      $netCfg = ConfigProxy::getFrameworkConfig('Net');
      if(!isset($netCfg['changYan']) || !isset($netCfg['changYan']['appid']) || !isset($netCfg['changYan']['appkey'])){
         $errorType = ErrorType::getInstance();
         Kernel\throw_exception(new Exception(
            $errorType->msg('E_SDK_CONFIG_NOT_EXIST'),
            $errorType->code('E_SDK_CONFIG_NOT_EXIST')
         ));
      }
      return $netCfg->changYan;
   }

   /**
    * @return \Zend\Http\Client
    */
   protected function getHttpClient()
   {
      if(null == $this->client){
         $this->client = new HttpClient();
      }
      return $this->client;
   }

   /**
    * @return \Phalcon\Cache\Backend\File
    */
   protected function getCacher()
   {
      if(null == $this->cacher){
         $this->cacher = Kernel\make_cache_object(implode(DS, array('Framework', 'Net', 'ChangYan' ,'AccessToken')), 7000);
      }
      return $this->cacher;
   }
}