<?php
/**
 * Cntysoft Cloud Software Team
 *
 * @author SOFTBOY <cntysoft@163.com>
 * @copyright  Copyright (c) 2010-2011 Cntysoft Technologies China Inc. <http://www.cntysoft.com>
 * @license    http://www.cntysoft.com/license/new-bsd     New BSD License
 */
namespace Cntysoft\Framework\ApiServer;

interface ApiAuthorizer
{
   /**
    * 检查当前的认证状态
    *
    * @param int $targetType 验证实体的类型
    * @param string $type API接口类型
    * @param string $key API接口识别ID
    * @param string $invokeParams 本次调用的参数
    * @param \Cntysoft\Framework\ApiServer\AbstractScript $handlerObject 当调用类型为 \Cntysoft\API_CALL_SYS 时候用于判断是否为派发器
    * @throw Exception
    */
   public function check($targetType, $type, $key, array $invokeParams = array(), $handlerObject = null);
}