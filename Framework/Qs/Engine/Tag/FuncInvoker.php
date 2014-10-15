<?php
/**
 * Cntysoft Cloud Software Team
 * 
 * @author SOFTBOY <cntysoft@163.com>
 * @copyright  Copyright (c) 2010-2011 Cntysoft Technologies China Inc. <http://www.cntysoft.com>
 * @license    http://www.cntysoft.com/license/new-bsd     New BSD License
 */
namespace Cntysoft\Framework\Qs\Engine\Tag;
use Cntysoft\Framework\Qs\Engine\Tag\Exception;
/**
 * 系统内置函数调用标签
 */
class FuncInvoker extends AbstractTag
{
    /**
     * @param string $type 函数类型
     * @param string $name 函数名称
     * @param array $params 调用参数
     */
    public function call($type,$name, array $params = array())
    {
        try{
            $params['type'] = $type;
            $this->invokeParams = &$params;
            $cls = '\\Qs\\Lib\\'.$type;
            if(!class_exists($cls)){
                throw new Exception(' Sys function class '.$cls.' is not exist');
            }
            if(!method_exists($cls, $name)){
                throw new Exception('method '.$name.' is not exist');
            }
            echo call_user_func_array($cls.'::'.$name, array_slice($params, 0, -1));
        } catch (\Exception $ex) {
            echo $this->renderError($ex->getMessage());
        }
    }

    /**
     * @inheritdoc
     */
    protected function getTagSignature()
    {
        $type = $this->invokeParams['type'];
        unset($this->invokeParams['type']);
        return sprintf('Qs::%s(%s)', $type, implode(',', $this->invokeParams));
    }

}