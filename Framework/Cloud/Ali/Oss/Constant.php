<?php
/**
 * Cntysoft Cloud Software Team
 * 
 * @author SOFTBOY <cntysoft@163.com>
 * @copyright  Copyright (c) 2010-2011 Cntysoft Technologies China Inc. <http://www.cntysoft.com>
 * @license    http://www.cntysoft.com/license/new-bsd     New BSD License
 */
namespace Cntysoft\Framework\Cloud\Ali\Oss;
final class Constant
{

   //HTTP方法
   const OSS_HTTP_GET = 'GET';
   const OSS_HTTP_PUT = 'PUT';
   const OSS_HTTP_HEAD = 'HEAD';
   const OSS_HTTP_POST = 'POST';
   const OSS_HTTP_DELETE = 'DELETE';
   const OSS_HTTP_OPTIONS = 'OPTIONS';
   //配置参数相关
   const OSS_OPT_HEADERS = 'headers';
   const OSS_OPT_CONTENT = 'content';
   const OSS_OPT_LENGTH = 'length';
   const OSS_OPT_BUCKET = 'bucket';
   const OSS_OPT_OBJECT = 'object';
   const OSS_OPT_METHOD = 'method';
   const OSS_OPT_QUERY = 'query';
   const OSS_OPT_MULTI_PART = 'uploads';
   const OSS_OPT_MULTI_DELETE = 'delete';
   const OSS_OPT_HOST = 'Host';
   const OSS_OPT_DATE = 'Date';
   const OSS_OPT_CONTENT_TYPE = 'Content-Type';
   const OSS_OPT_CONTENT_MD5 = 'Content-Md5';
   const OSS_OPT_QUERY_STRING = 'query_string';
   const OSS_OPT_PART_NUM = 'partNumber';
   const OSS_OPT_UPLOAD_ID = 'uploadId';
   const OSS_OPT_SUB_RESOURCE = 'sub_resource';
   const OSS_OPT_FILE_UPLOAD = 'fileUpload';
   const OSS_OPT_FILE_DOWNLOAD = 'fileDownload';
   const OSS_OPT_PART_SIZE = 'partSize';
   const OSS_OPT_SIZE = 'size';
   const OSS_OPT_PREAUTH = 'preauth';
   const OSS_OPT_CONTENT_LENGTH = 'Content-Length';
   const OSS_OPT_CHECK_MD5 = 'checkmd5';
   //http请求头相关常量
   const OSS_HEADER_ACL = 'x-oss-acl';
   const OSS_HEADER_OBJECT_GROUP = 'x-oss-file-group';
   const OSS_HEADER_OBJECT_COPY_SOURCE = 'x-oss-copy-source';
   const OSS_HEADER_OBJECT_COPY_SOURCE_RANGE = "x-oss-copy-source-range";
   const OSS_HEADER_SECURITY_TOKEN = "x-oss-security-token";
   const OSS_HEADER_CONTENT_MD5 = 'Content-Md5';
   const OSS_HEADER_CONTENT_TYPE = 'Content-Type';
   const OSS_HEADER_CONTENT_LENGTH = 'Content-Length';
   const OSS_HEADER_IF_MODIFIED_SINCE = 'If-Modified-Since';
   const OSS_HEADER_IF_UNMODIFIED_SINCE = 'If-Unmodified-Since';
   const OSS_HEADER_IF_MATCH = 'If-Match';
   const OSS_HEADER_IF_NONE_MATCH = 'If-None-Match';
   const OSS_HEADER_CACHE_CONTROL = 'Cache-Control';
   const OSS_HEADER_HOST = 'Host';
   const OSS_HEADER_DATE = 'Date';
   //oss权限相关常量
   const OSS_ACL_TYPE_PRIVATE = 'private';
   const OSS_ACL_TYPE_PUBLIC_READ = 'public-read';
   const OSS_ACL_TYPE_PUBLIC_READ_WRITE = 'public-read-write';
   const OSS_DEFAULT_PREFIX = 'x-oss-';
   //私有URL变量
   const OSS_URL_ACCESS_KEY_ID = 'OSSAccessKeyId';
   const OSS_URL_EXPIRES = 'Expires';
   const OSS_URL_SIGNATURE = 'Signature';
}