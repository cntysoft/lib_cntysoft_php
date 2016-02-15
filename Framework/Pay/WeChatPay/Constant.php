<?php
/**
 * Cntysoft Cloud Software Team
 * 
 * @author ZhiHui <liuyan2526@qq.com>
 * @copyright Copyright (c) 2010-2015 Cntysoft Technologies China Inc. <http://www.cntysoft.com>
 * @license http://www.cntysoft.com/license/new-bsd     New BSD License
*/
namespace Cntysoft\Framework\Pay\WeChatPay;

class Constant
{
   const WECHATPAY_UNIFIED_ORDER = 'https://api.mch.weixin.qq.com/pay/unifiedorder';
   const WECHATPAY_SELECT_ORDER = 'https://api.mch.weixin.qq.com/pay/orderquery';
   const WECHATPAY_CLOSE_ORDER = 'https://api.mch.weixin.qq.com/pay/closeorder';
   const WECHATPAY_ASKFOR_REFUND = 'https://api.mch.weixin.qq.com/secapi/pay/refund';
   const WECHATPAY_SELECT_REFUND = 'https://api.mch.weixin.qq.com/pay/refundquery';
   const WECHATPAY_DOWNLOAD_ORDER = 'https://api.mch.weixin.qq.com/pay/downloadbill';
   const WECHATPAY_REPORT = 'https://api.mch.weixin.qq.com/payitil/report';
   const WECHATPAY_LONGLINK_TO_SHORTLINK = 'https://api.mch.weixin.qq.com/tools/shorturl';
}