<?php
/**
 * Cntysoft Cloud Software Team
 *
 * @author SOFTBOY <cntysoft@163.com>
 * @copyright  Copyright (c) 2010-2011 Cntysoft Technologies China Inc. <http://www.cntysoft.com>
 * @license    http://www.cntysoft.com/license/new-bsd     New BSD License
 */
namespace Cntysoft\Framework\Net;
use Cntysoft\Stdlib\ErrorType as BaseErrorType;
class ErrorType extends BaseErrorType
{
   protected $map = array(
      //Mail
      'E_EMAIL_CONFIG_NOT_EXIT' => array(10001, 'The config of the email is not exit.'),
      'E_PARAM_IS_NOT_STRING_OR_ARRAY' => array(10002, 'The param needed is string or array, but the %s is given'),
      'E_EMAIL_CONTENT_IS_NOT_EXIT' => array(10003, 'The email content is not exit.'),
      //Download
      'E_DOWNLOAD_PATH_INVALID' => array(10004, 'The download path %s is not support.'),
      'E_DOWNLOAD_URL_INVALID' => array(10005, 'The download url is invalid.'),
      //Upload
      'E_FILE_CANOT_OVERWRITE' => array(10006, 'File %s could not be renamed. It already exists.'),
      'E_UPLOAD_FILE_NOT_EXIST' => array(10007, 'Upload file %s is not exist.'),
      'E_WAIT_MORE_CHUNK' => array(10008, 'Upload file %s need more chunk'),
      //FTP
      'E_FTP_REQUIRE_CFG_LEAK' => array(10009, 'Ftp instance create leak host , username or passwork'),
      'E_FTP_CONN_FAIL' => array(10010, 'Connect to Ftp server fail'),
      'E_FTP_CONN_NOT_EXIST' => array(10011, 'Current no active connection to Ftp server, cannot login'),
      'E_FTP_LOGIN_FAIL' => array(10012, 'Login in Ftp server fail'),
      'E_CURL_FTP_CONN_ERROR' => array(10013, 'curl ftp connection failure'),
      'E_CURL_FTP_NO_ACTIVE_CONN' => array(10014, 'curl ftp no active connection'),
      'E_CURL_ERROR' => array(10015, 'curl error : %s'),

      'E_UPLOAD_DIR_NOT_ALLOWED' => array(10016, 'target dir %s is not allowed'),
      'E_UPLOAD_PATH_EMPTY' => array(10017, 'upload path empty'),

      //Qiniu
      'E_QINIU_CALLBACK_TYPE_ERROR' => array(10018, 'qiniu callback type error'),
   );
}