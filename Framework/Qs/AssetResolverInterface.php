<?php
/**
 * Cntysoft Cloud Software Team
 *
 * @author Changwang <chenyongwang1104@163.com>
 * @copyright  Copyright (c) 2010-2011 Cntysoft Technologies China Inc. <http://www.cntysoft.com>
 * @license    http://www.cntysoft.com/license/new-bsd     New BSD License
 */
namespace Cntysoft\Framework\Qs;
/**
 *  静态文件的获取接口
 *
 * Interface AssetResolverInterface
 * @package Cntysoft\Framework\Qs
 */
interface AssetResolverInterface
{
   /**
    *  获取Css文件的基本路径
    *
    * @return mixed
    */
   public function getCssBasePath();

   /**
    *  获取静态文件的基本路径
    *
    * @return mixed
    */
   public function getAssetBasePath();

   /**
    *  获取图片文件的基本路径
    *
    * @return mixed
    */
   public function getImageBasePath();

   /**
    *  获取Js文件的基本路径
    *
    * @return mixed
    */
   public function getJsBasePath();
}