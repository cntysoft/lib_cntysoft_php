<?php
/**
 * Cntysoft Cloud Software Team
 * 
 * @author SOFTBOY <cntysoft@163.com>
 * @copyright  Copyright (c) 2010-2011 Cntysoft Technologies China Inc. <http://www.cntysoft.com>
 * @license    http://www.cntysoft.com/license/new-bsd     New BSD License
 */
namespace Cntysoft\Framework\Qs\Engine\Tag;
use Cntysoft\Kernel\StdHtmlPath;
use Cntysoft\Framework\Qs\Utils;
use Cntysoft\Framework\Qs\TagRefl;
/**
 * 抽象的label脚本
 */
abstract class AbstractLabelScript extends AbstractScript
{
    /**
     * 加载标签指定的渲染模板
     * 
     * @param string $tpl
     */
    public function loadRenderTpl($tpl)
    {
        include $tpl;
    }

    /**
     * 获取标签的Ui文件夹路径
     * 
     * @param string $id
     * @return string
     */
    public function getUiPath($id = null)
    {
        return $this->getSelfPath($id).'/'.TagRefl::TAG_UI_DIR_NAME;
    }

    /**
     * 获取标签的JS文件
     * 
     * @param string $id
     * @return string
     */
    public function getJsPath($id = null)
    {
        return $this->getUiPath($id).'/'.TagRefl::TAG_UI_JS_DIR_NAME;
    }

    /**
     * 获取标签Css路径
     * 
     * @param string $id
     * @return string
     */
    public function getCssPath($id = null)
    {
        return $this->getUiPath($id).'/'.TagRefl::TAG_UI_CSS_DIR_NAME;
    }

    /**
     * 加载Css文件
     * 
     * @param string $file
     * @return void
     */
    public function loadCss($file)
    {
        echo Utils::generateCssLinkTag($this->getCssPath(), $file);
    }

    /**
     * 加载本标签Js文件
     * 
     * @param string $file
     * @return void
     */
    public function loadJs($file)
    {
        echo Utils::generateJsScriptTag($this->getJsPath(), $file);
    }

    /**
     * 获取系统图片路径
     * 
     * @param string $id
     * @return string
     */
    public function getImagePath($id = null)
    {
        return $this->getUiPath($id).'/'.TagRefl::TAG_UI_IMAGE_DIR_NAME;
    }

    /**
     * 获取系统Lib路径
     * 
     * @param string $id
     * @return string
     */
    public function getLibPath($id = null)
    {
        return $this->getSelfPath($id).'/'.TagRefl::TAG_LIB_DIR_NAME;
    }

    /**
     * 指定一个标签的ID
     * 
     * @param string $id
     * @return string
     */
    public function getSelfPath($id = null)
    {
        if (!$id) {
            $id = $this->getParam('id');
        }
        return StdHtmlPath::getTagLibPath().'/'.TagRefl::T_LABLE.'/'.$id;
    }

}