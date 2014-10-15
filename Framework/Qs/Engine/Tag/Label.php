<?php
/**
 * Cntysoft Cloud Software Team
 * 
 * @author SOFTBOY <cntysoft@163.com>
 * @copyright  Copyright (c) 2010-2011 Cntysoft Technologies China Inc. <http://www.cntysoft.com>
 * @license    http://www.cntysoft.com/license/new-bsd     New BSD License
 */
namespace Cntysoft\Framework\Qs\Engine\Tag;
use Zend\Stdlib\ErrorHandler;
use Cntysoft\Framework\Qs\View;
use Cntysoft\Framework\Qs\ErrorType;
use Cntysoft\Kernel;
/**
 * 解析label类型的标签
 */
class Label extends AbstractTag
{
    CONST LABEL_SCRIPT_FILENAME = 'Script.php';
    
    /**
     * 正常渲染标签
     * 
     * @param array $params
     */
    public function render($id, array &$params = array())
    {
        try {
            ErrorHandler::start(\E_ALL);
            $params['id'] = $id;
            $this->invokeParams = &$params;
            $meta = $this->prepareParse(View::TAG_LABEL, $params);
            $tagDir = $this->getTagDir(View::TAG_LABEL, $id);
            $specifyTpl = isset($params['renderTpl']) ? $params['renderTpl'] : null;
            if (!$meta['static']) {
                //加载script脚本
                $script = Kernel\real_path($tagDir.DS.self::LABEL_SCRIPT_FILENAME);
                if (!file_exists($script)) {
                    $errorType = ErrorType::getInstance();
                    throw new Exception(
                    $errorType->msg('E_TAG_SCRIPT_FILE_NOT_EXIST'), $errorType->code('E_TAG_SCRIPT_FILE_NOT_EXIST'));
                }
                if (!$specifyTpl) {
                    if (isset($params['ajax']) && $params['ajax']) {
                        $tpl = 'Default.ajax.phtml';
                    } else {
                        $tpl = 'Default.phtml';
                    }
                } else {
                    $tpl = $specifyTpl.'.phtml';
                }

                
            } else {
                if ($specifyTpl) {
                    $tpl = $specifyTpl.'.phtml';
                } else {
                    $tpl = 'Default.phtml';
                }
            }

            $tpl = Kernel\real_path($tagDir.DS.$tpl);
            if (!file_exists($tpl)) {
                $errorType = ErrorType::getInstance();
                throw new Exception(
                $errorType->msg('E_TAG_TPL_FILE_NOT_EXIST'), $errorType->code('E_TAG_TPL_FILE_NOT_EXIST'));
            }
            //不是静态的话需要加入脚本
            //这个时候 tpl执行
            if (!$meta['static']) {
                 include_once $script;
                 //检查脚本类是否存在
                 $cls = self::TAGLIB_BASE_NS.'\\'.View::TAG_LABEL.'\\'.$meta['namespace'].'\\'.$meta['class'];
                 if(!class_exists($cls)){
                     $errorType = ErrorType::getInstance();
                     throw new Exception($errorType->msg('E_TAG_CLS_NOT_EXIST', $cls),
                             $errorType->code('E_TAG_CLS_NOT_EXIST'));
                 }
                 $scriptObj = new $cls($params, $this->engine);
                 $scriptObj->loadRenderTpl($tpl);
            }else{
                 include $tpl;
            }
            ErrorHandler::stop();
        } catch (\Exception $ex) {
            $this->renderError($ex->getMessage());
        }
    }

    /**
     * @inheritdoc
     */
    protected function getTagSignature()
    {
        if (!empty($this->invokeParams)) {
            $paramMsg = var_export($this->invokeParams, true);
        } else {
            $paramMsg = '';
        }
        return 'Qs::Label('.$paramMsg.')';
    }

}