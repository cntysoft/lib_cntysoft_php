<?php
/**
 * Cntysoft Cloud Software Team
 *
 * @author ZhiHui <liuyan2526@qq.com>
 * @copyright  Copyright (c) 2010-2011 Cntysoft Technologies China Inc. <http://www.cntysoft.com>
 * @license    http://www.cntysoft.com/license/new-bsd     New BSD License
 */
namespace Cntysoft\Framework\Net\ChangYan;
use Cntysoft\Stdlib\ErrorType as BaseErrorType;
class ErrorType extends BaseErrorType
{
   protected $map = array(
      'E_NOT_HAVE_WECHAT_APPID_APPSECRET' => array(10001, '没有微信AppId和AppSecret'),
      'E_REQUEST_ERROR'                   => array(10002, 'request错误 ')
   );

}