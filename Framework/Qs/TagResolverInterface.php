<?php
/**
 * Cntysoft Cloud Software Team
 *
 * @author SOFTBOY <cntysoft@163.com>
 * @copyright  Copyright (c) 2010-2011 Cntysoft Technologies China Inc. <http://www.cntysoft.com>
 * @license    http://www.cntysoft.com/license/new-bsd     New BSD License
 */
namespace Cntysoft\Framework\Qs;

interface TagResolverInterface
{
   /**
    * 获取标签基本文件夹路径
    *
    * @return string
    */
   public function getTagBaseDir();

   /**
    * 获取标签的基本名称空间
    *
    * @return string
    */
   public function getTagLabelBaseNs();

   /**
    * 获取数据源标签的基本名称空间
    *
    * @return string
    */
   public function getTagDsBaseNs();
}