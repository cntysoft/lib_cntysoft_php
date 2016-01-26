<?php

namespace Cntysoft\Framework\Pay\WeChatPay;
use Cntysoft\Stdlib\ErrorType as BaseErrorType;
class ErrorType extends BaseErrorType
{
   protected $map = array(
      'E_WECHATPAY_UNIFIEDORDER_NO_PARAMS_BODY'       => array(10001, '缺少订单描述信息'),
      'E_WECHATPAY_UNIFIEDORDER_NO_PARAMS_OUTTRADENO' => array(10002, '缺少订单号'),
      'E_WECHATPAY_UNIFIEDORDER_NO_PARAMS_TOTALFEE'   => array(10003, '缺少订单总金额信息'),
      'E_WECHATPAY_UNIFIEDORDER_NO_PARAMS_CLIENTIP'   => array(10004, '缺少订单客户ip信息'),
      'E_WECHATPAY_UNIFIEDORDER_NO_PARAMS_TRADETYPE'  => array(10005, '缺少订单交易类型信息'),
      'E_PAY_WECHAT_DATA_XML_ERROR'                   => array(10006, 'xml数据异常！'),
      'E_WECHATPAY_LONGTOSHORT_NO_PARAMS_URL'         => array(10007, '缺少连接地址'),
      'E_WECHATPAY_SELECTREFUND_NO_PARAMS_ODDERID'    => array(10008, '缺少订单号'),
      'E_WECHATPAY_ASKFORREFUND_NO_PARAMS_ORDERID'    => array(10009, '缺少订单号'),
      'E_WECHATPAY_ASKFORREFUND_NO_PARAMS_REFUNDID'   => array(10010, '缺少退款单号'),
      'E_WECHATPAY_ASKFORREFUND_NO_PARAMS_TOTALFEE'   => array(10011, '缺少总金额数'),
      'E_WECHATPAY_ASKFORREFUND_NO_PARAMS_REFUNDFEE'  => array(10012, '缺少退款金额数'),
      'E_WECHATPAY_ASKFORREFUND_NO_PARAMS_OPUSERID'   => array(10013, '缺少操作员工号')
   );

}