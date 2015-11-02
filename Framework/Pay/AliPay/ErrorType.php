<?php
/**
 * Cntysoft Cloud Software Team
 * 
 * @author Changwang <chenyongwang1104@163.com>
 * @copyright Copyright (c) 2010-2011 Cntysoft Technologies China Inc. <http://www.cntysoft.com>
 * @license http://www.cntysoft.com/license/new-bsd     New BSD License
*/
namespace Cntysoft\Framework\Pay\AliPay;
use Cntysoft\Stdlib\ErrorType as BaseErrorType;

class ErrorType extends BaseErrorType
{
    protected $map = array(
       'ILLEGAL_SIGN' => array(10001, '签名不正确'),
       'ILLEGAL_DYN_MD5_KEY' => array(10002, '动态密钥信息错误'),
       'ILLEGAL_ENCRYPT' => array(10003, '加密不正确'),
       'ILLEGAL_ARGUMENT' => array(10004, '参数不正确'),
       'ILLEGAL_SERVICE' => array(10005, '接口名称不正确'),
       'ILLEGAL_PARTNER' => array(10006, '合作伙伴ID不正确'),
       'ILLEGAL_EXTERFACE' => array(10007, '接口配置不正确'),
       'ILLEGAL_PARTNER_EXTERFACE' => array(10008, '合作伙伴接口信息不正确'),
       'ILLEGAL_SECURITY_PROFILE' => array(10009, '未找到匹配的密钥配置'),
       'ILLEGAL_AGENT' => array(10010, '代理ID不正确'),
       'ILLEGAL_SIGN_TYPE' => array(10011, '签名类型不正确'),
       'ILLEGAL_CHARSET' => array(10012, '字符集不合法'),
       'ILLEGAL_CLIENT_IP' => array(10013, '客户端IP地址无权访问服务'),
       'ILLEGAL_DIGEST_TYPE' => array(10014, '摘要类型不正确'),
       'ILLEGAL_DIGEST' => array(10015, '文件摘要不正确'),
       'ILLEGAL_FILE_FORMAT' => array(10016, '文件格式不正确'),
       'ILLEGAL_ENCODING' => array(10017, '不支持该编码类型'),
       'ILLEGAL_REQUEST_REFERER' => array(10018, '防钓鱼检查不支持该请求来源'),
       'ILLEGAL_ANTI_PHISHING_KEY' => array(10019, '防钓鱼检查非法时间戳参数'),
       'ANTI_PHISHING_KEY_TIMEOUT' => array(10020, '防钓鱼检查时间戳超时'),
       'ILLEGAL_EXTER_INVOKE_IP' => array(10021, '防钓鱼检查非法调用IP'),
       'ILLEGAL_NUMBER_FORMAT' => array(10022, '数字格式不合法'),
       'ILLEGAL_INTEGER_FORMAT' => array(10023, 'Int类型格式不合法'),
       'ILLEGAL_MONEY_FORMAT' => array(10024, '金额格式不合法'),
       'ILLEGAL_DATA_FORMAT' => array(10025, '日期格式错误'),
       'REGEXP_MATCH_FAIL' => array(10026, '正则表达式匹配失败'),
       'ILLEGAL_LENGTH' => array(10027, '参数值长度不合法'),
       'PARAMTER_IS_NULL' => array(10028, '参数值为空'),
       'HAS_NO_PRIVILEGE' => array(10029, '无权访问'),
       'SYSTEM_ERROR' => array(10030, '支付宝系统错误'),
       'SESSION_TIMEOUT' => array(10031, 'session超时'),
       'ILLEGAL_TARGET_SERVICE' => array(10032, '错误的target_service'),
       'ILLEGAL_ACCESS_SWITCH_SYSTEM' => array(10033, 'partner不允许访问该类型的系统'),
       'ILLEGAL_SWITCH_SYSTEM' => array(10034, '切换系统异常'),
       'EXTERFACE_IS_CLOSED' => array(10035, '接口已关闭')
    );
}
