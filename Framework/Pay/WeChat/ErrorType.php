<?php
/**
 * Cntysoft Cloud Software Team
 * 
 * @author Arvin <cntyfeng@163.com>
 * @copyright Copyright (c) 2010-2015 Cntysoft Technologies China Inc. <http://www.cntysoft.com>
 * @license http://www.cntysoft.com/license/new-bsd     New BSD License
*/
namespace Cntysoft\Framework\Pay\WeChat;
use Cntysoft\Stdlib\ErrorType as BaseErrorType;

class ErrorType extends BaseErrorType
{
   protected $map = array(
      'E_PAY_WECHAT_DATA_ARRAY_ERROR' => array(10001, '数组数据异常！'),
      'E_PAY_WECHAT_DATA_XML_ERROR' => array(10002, 'xml数据异常！'),
      'E_PAY_WECHAT_DATA_SIGN_ERROR' => array(10003, '签名错误！'),
      'E_PAY_WECHAT_UNIFIED_ORDER_NEED_OUT_TRADE_NO' => array(10004, '缺少统一支付接口必填参数out_trade_no！'),
      'E_PAY_WECHAT_UNIFIED_ORDER_NEED_BODY' => array(10005, '缺少统一支付接口必填参数body！'),
      'E_PAY_WECHAT_UNIFIED_ORDER_NEED_TOTAL_FEE' =>array(10006, '缺少统一支付接口必填参数total_fee！'),
      'E_PAY_WECHAT_UNIFIED_ORDER_NEED_TRADE_TYPE' => array(10007, '缺少统一支付接口必填参数trade_type！'),
      'E_PAY_WECHAT_UNIFIED_ORDER_NEED_OPEN_ID'=>array(10008, '统一支付接口中，缺少必填参数openid！trade_type为JSAPI时，openid为必填参数！'),
      'E_PAY_WECHAT_UNIFIED_ORDER_NEED_PRODUCT_ID' => array(10009, '统一支付接口中，缺少必填参数product_id！trade_type为NATIVE时，product_id为必填参数！'),
      'E_PAY_WECHAT_CURL_ERROR' => array(10010, 'curl出错，错误码: %s'),
      'E_PAY_WECHAT_REPORT_NEED_INTERFACE_URL'=> array(10011, '接口URL，缺少必填参数interface_url！'),
      'E_PAY_WECHAT_REPORT_NEED_RETURN_CODE' => array(10012, '返回状态码，缺少必填参数return_code！'),
      'E_PAY_WECHAT_REPORT_NEED_RESULT_CODE' => array(10013, '业务结果，缺少必填参数result_code！'),
      'E_PAY_WECHAT_REPORT_NEED_USER_IP' => array(10014, '访问接口IP，缺少必填参数user_ip！'),
      'E_PAY_WECHAT_REPORT_NEED_EXECUTE_TIME_' => array(10015, '接口耗时，缺少必填参数execute_time_！'),
      'E_PAY_WECHAT_ORDER_QUERY_NEED_OUT_TRADE_NO_OR_TRANSACTION_ID' => array(10016, '订单查询接口中，out_trade_no、transaction_id至少填一个！'),
      'E_PAY_WECHAT_ORDER_CLOSE_NEED_OUT_TRADE_NO' => array(10017, '订单查询接口中，out_trade_no必填！'),
      'E_PAY_WECHAT_REFUND_NEED_OUT_TRADE_NO_OR_TRANSACTION_ID' => array(10018, '退款申请接口中，out_trade_no、transaction_id至少填一个！'),
      'E_PAY_WECHAT_REFUND_NEED_OUT_REFUND_NO' => array(10019, '退款申请接口中，缺少必填参数out_refund_no！'),
      'E_PAY_WECHAT_REFUND_NEED_TOTAL_FEE' => array(10020, '退款申请接口中，缺少必填参数total_fee！'),
      'E_PAY_WECHAT_REFUND_NEED_REFUND_FEE' => array(10021, '退款申请接口中，缺少必填参数refund_fee！'),
      'E_PAY_WECHAT_REFUND_NEED_OP_USER_ID' => array(10022, '退款申请接口中，缺少必填参数op_user_id！'),
      'E_PAY_WECHAT_REFUND_QUERY_NEED_OUT_REFUND_NO_OR_OUT_TRADE_NO_OR_TRANSACTION_ID_OR_REFUND_ID' => array(10023, '退款查询接口中，out_refund_no、out_trade_no、transaction_id、refund_id四个参数必填一个！'),
      'E_PAY_WECHAT_BILL_NEED_BILL_DATE' => array(10024, '对账单接口中，缺少必填参数bill_date！'),
      'E_PAY_WECHAT_MICRO_PAY_NEED_BODY' => array(10025, '提交被扫支付API接口中，缺少必填参数body！'),
      'E_PAY_WECHAT_MICRO_PAY_NEED_OUT_TRADE_NO' => array(10026, '提交被扫支付API接口中，缺少必填参数out_trade_no！'),
      'E_PAY_WECHAT_MICRO_PAY_NEED_TOTAL_FEE' => array(10027, '提交被扫支付API接口中，缺少必填参数total_fee！'),
      'E_PAY_WECHAT_MICRO_PAY_NEED' => array(10028, '提交被扫支付API接口中，缺少必填参数auth_code！'),
      'E_PAY_WECHAT_REVERSE_NEED_OUT_TRADE_NO_OR_TRANSACTION_ID' => array(10029, '撤销订单API接口中，参数out_trade_no和transaction_id必须填写一个！'),
      'E_PAY_WECHAT_SHORT_URL_NEED_LONG_URL' => array(10030, '需要转换的URL，签名用原串，传输需URL encode！'),
      'E_PAY_WECHAT_JSAPI_PARAM_ERROR' => array(10031, '参数错误')
   );
}
