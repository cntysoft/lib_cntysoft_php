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
class JsApiPay
{
   /**
    * 获取扫码支付二维码信息
    * 
    * @param array $order 扫码支付订单信息
    * @return array
    */
   public function getPrepayId(array $order,array $config)
   {
      $api = new Api($config);
      $res = $api->unifiedOrder($order);
      if ($res['sign'] == ShareFunction::createSign($res)) {
         return array(
            'prepay_id' => $res['prepay_id']
         );
      }
   }

}

