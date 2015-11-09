<?php
/**
 * Cntysoft Cloud Software Team
 * 
 * @author Arvin <cntyfeng@163.com>
 * @copyright Copyright (c) 2010-2015 Cntysoft Technologies China Inc. <http://www.cntysoft.com>
 * @license http://www.cntysoft.com/license/new-bsd     New BSD License
*/
namespace Cntysoft\Framework\Pay\WeChat;

class NativePay
{
	/**
    * 获取微信支付的url地址，这里是第二种模式
    * 
    * @param string $body 商品名称
    * @param integer $price 订单价格
    * @param integer $orderId 订单id
    * @param string $feeType 货币类型
    * @return array
    */
	public function getPayUrl($body, $price, $orderId, $feeType = 'CNY')
	{
      $native = new Utils();
//      $native->setValue('device_info', 'WEB');
//      $native->setValue('product_id', $productId);
      $native->setValue('trade_type', 'NATIVE');
      $native->setValue('total_fee', $price);
      $native->setValue('out_trade_no', $orderId);
      $native->setValue('body', $body);
      $native->setValue('fee_type', $feeType);
      
      $ret = WeChatPayApi::unifiedOrder($native);
      
      return $ret;
	}
   
   /**
    * 关闭订单
    * 
    * @param integer $orderId
    * @return array
    */
   public function closeOrder($orderId)
   {
      $native = new Utils();
      $native->setValue('out_trade_no', $orderId);
      
      return WeChatPayApi::closeOrder($native);
   }
}

