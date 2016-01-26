<?php
namespace Cntysoft\Framework\Pay\WeChatPay;

class Constant
{
   const APPID = 'wx61a19cda5d4d8a9d';
   const MCH_ID = '1273867401';
   const API_KEY = '61851df3711f2567d6e53a3af15f8c24';
   const NOTIFY_URL = '/';
   const WECHATPAY_UNIFIED_ORDER = 'https://api.mch.weixin.qq.com/pay/unifiedorder';
   const WECHATPAY_SELECT_ORDER = 'https://api.mch.weixin.qq.com/pay/orderquery';
   const WECHATPAY_CLOSE_ORDER = 'https://api.mch.weixin.qq.com/pay/closeorder';
   const WECHATPAY_ASKFOR_REFUND = 'https://api.mch.weixin.qq.com/secapi/pay/refund';
   const WECHATPAY_SELECT_REFUND = 'https://api.mch.weixin.qq.com/pay/refundquery';
   const WECHATPAY_DOWNLOAD_ORDER = 'https://api.mch.weixin.qq.com/pay/downloadbill';
   const WECHATPAY_REPORT = 'https://api.mch.weixin.qq.com/payitil/report';
   const WECHATPAY_LONGLINK_TO_SHORTLINK = 'https://api.mch.weixin.qq.com/tools/shorturl';
}