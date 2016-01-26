<?php
/**
 * Cntysoft Cloud Software Team
 * 
 * @author ZhiHui <liuyan2526@qq.com>
 * @copyright Copyright (c) 2010-2015 Cntysoft Technologies China Inc. <http://www.cntysoft.com>
 * @license http://www.cntysoft.com/license/new-bsd     New BSD License
*/
namespace Cntysoft\Framework\Pay\WeChatPay;
use Cntysoft\Kernel;
/**
 * 本类实现web扫码支付
 */
class NativePay
{
   /**
    * 获取扫码支付二维码信息
    * 
    * @param array $order 扫码支付订单信息
    * @return array
    */
   public function getCodeUrl(array $order)
   {
      $api = new Api();
      $res = $api->unifiedOrder($order);
      return array(
         'code_url' => $res['code_url']
      );
   }
}