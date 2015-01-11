<?php
/**
 * Cntysoft Cloud Software Team
 *
 * @author SOFTBOY <cntysoft@163.com>
 * @copyright  Copyright (c) 2010-2011 Cntysoft Technologies China Inc. <http://www.cntysoft.com>
 * @license    http://www.cntysoft.com/license/new-bsd     New BSD License
 */
namespace Cntysoft\Framework\Net\ChangYan;
use Cntysoft\Stdlib\ErrorType as BaseErrorType;
class ErrorType extends BaseErrorType
{
   protected $map = array(
      'E_ACCOUNT_ALREADY_BINDED' => array(10001, 'account already binded'),
      'E_REQUEST_ERROR' => array(10002, 'request error '),
      'E_APPLY_SETTING_ERROR' => array(10003, 'apply setting error : %s'),
      'E_SDK_CONFIG_NOT_EXIST' => array(10004, 'sdk appid or appkey not exist'),
      'E_API_INVOKE_ERROR' => array(10005, 'api invoke error : %s'),
      'E_ACCESS_TOKEN_EXPIRED' => array(10006, 'access token expired')
   );
}