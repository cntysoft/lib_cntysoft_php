<?php
/**
 * Cntysoft Cloud Software Team
 *
 * @author ZhiHui <liuyan2526@qq.com>
 * @copyright  Copyright (c) 2010-2011 Cntysoft Technologies China Inc. <http://www.cntysoft.com>
 * @license    http://www.cntysoft.com/license/new-bsd     New BSD License
 */
namespace Cntysoft\Framework\Cloud\WeChat;
class Constant
{
   const WECHAT_APP_APPID_APPSECRET_KEY = 'wechat';
   const LANG_TEXT_ZH_CN = 'zh_CN';
   const LANG_TEXT_ZH_TW = 'zh_TW';
   const LANG_TEXT_EN = 'en';
   
   const WECHAT_GET_ACCESSTOKEN_URL = 'https://api.weixin.qq.com/cgi-bin/token';
   const WECHAT_GET_IP_LIST = 'https://api.weixin.qq.com/cgi-bin/getcallbackip';
   const WECHAT_GET_USERS_LIST = 'https://api.weixin.qq.com/cgi-bin/user/get';
   const WECHAT_GET_USER_DETAIL = 'https://api.weixin.qq.com/cgi-bin/user/info';
   const WECAHT_SET_USER_REMARK = 'https://api.weixin.qq.com/cgi-bin/user/info/updateremark';
   const WECHAT_CREATE_USER_GROUPS = 'https://api.weixin.qq.com/cgi-bin/groups/create';
   const WECHAT_GET_USER_GROUPS_LIST = 'https://api.weixin.qq.com/cgi-bin/groups/get';
   const WECHAT_GET_GROUP_BY_USER_OPENID = 'https://api.weixin.qq.com/cgi-bin/groups/getid';
   const WECHAT_MODIFY_GROUP_NAME = 'https://api.weixin.qq.com/cgi-bin/groups/update';
   const WECHAT_MOVE_USER_TO_GROUP = 'https://api.weixin.qq.com/cgi-bin/groups/members/update';
   const WECHAT_MOVE_MULTI_USER_TO_GROUP = 'https://api.weixin.qq.com/cgi-bin/groups/members/batchupdate';
   const WECHAT_DELETE_GROUP = 'https://api.weixin.qq.com/cgi-bin/groups/delete';
   const WECHAT_MENU_CREATE = 'https://api.weixin.qq.com/cgi-bin/menu/create';
   const WECHAT_MENU_GET = 'https://api.weixin.qq.com/cgi-bin/menu/get';
   const WECHAT_MENU_DELETE = 'https://api.weixin.qq.com/cgi-bin/menu/delete';
   const WECHAT_MENU_GET_CONFIG = 'https://api.weixin.qq.com/cgi-bin/get_current_selfmenu_info';
   const WECHAT_GET_ACCESS_TOKEN_BY_CODE = 'https://api.weixin.qq.com/sns/oauth2/access_token';
   const WECHA_WEB_GET_USER_INFO = 'https://api.weixin.qq.com/sns/userinfo';
   
}