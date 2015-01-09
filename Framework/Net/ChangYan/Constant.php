<?php
/**
 * Cntysoft Cloud Software Team
 *
 * @author SOFTBOY <cntysoft@163.com>
 * @copyright  Copyright (c) 2010-2011 Cntysoft Technologies China Inc. <http://www.cntysoft.com>
 * @license    http://www.cntysoft.com/license/new-bsd     New BSD License
 */
namespace Cntysoft\Framework\Net\ChangYan;

final class Constant
{
   const MAIN_ENTRY = 'http://changyan.sohu.com';
   const LOGIN_ENTRY = 'http://changyan.sohu.com/login';
   const SETTING_COMMON_FURTHER_ENTRY = 'http://changyan.sohu.com/setting/common/further';
   const SETTING_CALLBACK_ENTRY = 'http://changyan.sohu.com/setting/further/save/apicallback';
   const SETTING_PUSH_BACK_ENTRY = 'http://changyan.sohu.com/setting/further/save/pushbackcommenturl';
   const AUTHORIZE_POINT = 'oauth2/authorize';
   const ACCESS_TOKEN_POINT = 'oauth2/token';

   const API_ENTRY = 'http://changyan.sohu.com/api';

   const DISPLAY_T_WEB = 'default';
   const DISPLAY_T_MOBILE= 'mobile';

   const PLATFORM_T_SINA = 2;
   const PLATFORM_T_QQ = 3;
   const PLATFORM_T_SOHU = 11;
}