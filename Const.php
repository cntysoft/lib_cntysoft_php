<?php
/**
 * Cntysoft Cloud Software Team
 *
 * @author SOFTBOY <cntysoft@163.com>
 * @copyright  Copyright (c) 2010-2011 Cntysoft Technologies China Inc. <http://www.cntysoft.com>
 * @license    http://www.cntysoft.com/license/new-bsd     New BSD License
 */
namespace Cntysoft;

//定义一些重要的物理路经
define('CNTY_STATICS_DIR', CNTY_ROOT_DIR.DS.'Statics');
define('CNTY_TEMPLATE_DIR', CNTY_STATICS_DIR.DS.'Templates');
define('CNTY_TAG_DIR', CNTY_ROOT_DIR.DS.'TagLibrary');
define('CNTY_JS_LIB_DIR', CNTY_ROOT_DIR.DS.'JsLibrary');
define('CNTY_JS_SYS_DIR', CNTY_ROOT_DIR.DS.'SysManager');
define('CNTY_SKIN_DIR', CNTY_STATICS_DIR.DS.'Skins');
define('CNTY_DATA_DIR', CNTY_ROOT_DIR.DS.'Data');
define('CNTY_CACHE_DIR', CNTY_DATA_DIR.DS.'Cache');
define('CNTY_UPLOAD_DIR', CNTY_DATA_DIR.DS.'UploadFiles');

//自定义名称
const LINUX = 'Linux';
const WINDOWS = 'WINNT';

//定义几个API请求数据键值
const INVOKE_PARAM_KEY = 'REQUEST_DATA';
const INVOKE_META_KEY = 'REQUEST_META';
const INVOKE_SECURITY_KEY = 'REQUEST_SECURITY';

/**
 * API调用的种类
 */
const API_CALL_SYS = 'Sys';
const API_CALL_APP = 'App';
const API_CALL_EXP_KEY = 'ApiCallException';
const SESSION_NS = 'CNTYSOFT_S_NS';
const SYS_AUTH_CODE_KEY = 'SYS_AUTH_CODE_KEY';

const STD_DATE_FORMAT = 'Y-m-d H:i:s';
//站点状态
const SITE_RUNNING = 1;
const SITE_STOP = 0;

// 前端用户特殊指定的情况
const T_F_USER = 1;
//前端按照会员组处理的方式
const T_F_GROUP = 2;
//验证实体为后端WEBOS API请求
const T_WEBOS = 4;

//标准异常上下文
const STD_EXCEPTION_CONTEXT = 'Cntysoft/Kernel/StdErrorType';

//一些数据类型定义
const INTEGER = 'integer';
const BOOLEAN = 'boolean';
const STRING = 'string';
const PHP_ARRAY = 'array';
const CATEGORY_ID = 'category'; //节点树类型

//标准分页大小
const STD_PAGE_SIZE = 15;

const CATEGORY_ROUTE_N_PAGE = '/category/{CategoryId}.html';
const CATEGORY_ITEM_ROUTE_N_PAGE = '/item/{ItemId}.html';

const CATEGORY_ROUTE_W_PAGE = '/category/{CategoryId}/page/{PageId}.html';
const CATEGORY_ITEM_ROUTE_W_PAGE = '/item/{ItemId}/page/{PageId}.html';
//列表静态模板
const LIST_HTML_URL_FORMAT = '%s/List_%d.html';
const ITEM_HTML_URL_FORMAT = '%s/items/%s/%s.html';
//标准内容分页符号
const CONTENT_PAGE_SEPARATOR = '<div style="page-break-after: always;"><span style="display:none">&nbsp;</span></div>';
//一些重要的全局键值
const CNF_VENDER_FRAMEWORK = 'venderFramework';

const UI_MODE_STD = 1;
const UI_MODE_CUSTOMIZE = 2;

const UTF8 = 'utf-8';
const GBK = 'gbk';
const GB2312 = 'gb2312';

//Asset相关文件夹名称
const CSS = 'Css';
const IMAGE = 'Images';
const JS = 'Js';
