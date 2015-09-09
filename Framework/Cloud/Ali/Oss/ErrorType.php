<?php
/**
 * Cntysoft Cloud Software Team
 *
 * @author SOFTBOY <cntysoft@163.com>
 * @copyright  Copyright (c) 2010-2011 Cntysoft Technologies China Inc. <http://www.cntysoft.com>
 * @license    http://www.cntysoft.com/license/new-bsd     New BSD License
 */
namespace Cntysoft\Framework\Cloud\Ali\Oss;
use Cntysoft\Stdlib\ErrorType as BaseErrorType;
class ErrorType extends BaseErrorType
{

   protected $map = array(
      'E_OSS_INVALID_HTTP_BODY_CONTENT' => array(10001, 'Http Body的内容非法'),
      'E_OSS_NOT_SET_HTTP_CONTENT' => array(10002, '未设置Http Body'),
      'E_OSS_INVALID_CONTENT_LENGTH' => array(10003, '非法的Content-Length值'),
      'E_OSS_CONTENT_LENGTH_MUST_MORE_THAN_ZERO' => array(10004, 'Content-Length必须大于0'),
      'E_OSS_CANOT_EMPTY' => array(10005, '不能为空,详情 : %s'),
      'E_OSS_BUCKET_NAME_INVALID' => array(10006, 'bucket: %s 未通过Bucket名称规则校验'),
      'E_OSS_OBJECT_NAME_INVALID' => array(10007, 'object: %s 未通过Object名称规则校验'),
      'E_OSS_ACL_INVALID' => array(10008, 'ACL不在允许范围,目前仅允许(private,public-read,public-read-write三种权限)'),
      'E_OSS_OBJECT_IS_NOT_ALLOWED_EMPTY' => array(10009, 'Object不允许为空'),
      'E_OSS_BUCKET_IS_NOT_ALLOWED_EMPTY' => array(10010, 'Bucket不允许为空'),
      'E_OSS_FILE_PATH_IS_NOT_ALLOWED_EMPTY' => array(10011, '上传文件路径为空'),
      'E_OSS_FILE_NOT_EXIST'=> array(10012 , '文件不存在'),
      'E_OSS_INIT_MULTI_PARTUPLOAD_FAIL' => array(10013, '初始化分块上传失败'),
      'E_OSS_OPTION_REQUIRE_FILED' => array(10014, 'option配置参数缺少：%s'),
      'E_OSS_MULTI_PART_UPLOAD_ERROR' => array(10015, '分段上传某个部分出错，请重试'),
      'E_OSS_G_ERROR' => array(10016, '操作出现错误 : %s')
   );
}