<?php
/**
 * Cntysoft Cloud Software Team
 * 
 * @author Arvin <cntyfeng@163.com>
 * @copyright Copyright (c) 2010-2015 Cntysoft Technologies China Inc. <http://www.cntysoft.com>
 * @license http://www.cntysoft.com/license/new-bsd     New BSD License
*/
namespace Cntysoft\Framework\Pay\WeChat;
use Cntysoft\Kernel;

class AppPay
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
	public function GetPayUrl($body, $price, $orderId, $feeType = 'CNY')
	{
      $app = new Utils();
//      $app->setValue('device_info', 'WEB');
//      $app->setValue('product_id', $productId);
      $app->setValue('trade_type', 'APP');
      $app->setValue('total_fee', $price);
      $app->setValue('out_trade_no', $orderId);
      $app->setValue('body', $body);
      $app->setValue('fee_type', $feeType);
      
      $ret = WeChatPayApi::unifiedOrder($app);
      
      return $ret;
	}
}

