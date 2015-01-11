<?php
/**
 * Cntysoft Cloud Software Team
 *
 * @author SOFTBOY <cntysoft@163.com>
 * @copyright  Copyright (c) 2010-2011 Cntysoft Technologies China Inc. <http://www.cntysoft.com>
 * @license    http://www.cntysoft.com/license/new-bsd     New BSD License
 */
namespace Cntysoft\Framework\Net\Msg;
use Cntysoft\Stdlib\ErrorType as BaseErrorType;

class ErrorType extends BaseErrorType
{
   protected $map = array(
      'E_USER_OR_PWD_ERROR' => array(10001, 'user or password error'),
      'E_EMPTY_NUMBER' => array(10002, 'phone number is empty'),
      'E_NUMBER_INVALID' => array(10003, 'phone number is invalid'),
      'E_CONTENT_INVALID' => array(10004, 'content invalid'),
      'E_CONTENT_HAS_INVALID_KEYWORD' => array(10005, 'content has invalid key word'),
      'E_SIGN_NOT_EXIST' => array(10006, 'msg sign is not exist'),
      'E_MSG_PLATFORM_CLOSED' => array(10007, 'msg platform closed'),
      'E_MSG_POOL_EMPTY' => array(10008, 'msg pool is empty'),
      'E_REQUEST_ERROR' => array(10009, 'request error '),
      'E_MSG_TOO_LONG' => array(10010, 'msg content is too long')
   );
}