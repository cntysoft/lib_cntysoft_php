<?php
/**
 * Cntysoft Cloud Software Team
 *
 * @author SOFTBOY <cntysoft@163.com>
 * @copyright  Copyright (c) 2010-2011 Cntysoft Technologies China Inc. <http://www.cntysoft.com>
 * @license    http://www.cntysoft.com/license/new-bsd     New BSD License
 */
namespace Cntysoft\Framework\Utils;
use PHPImageWorkshop\ImageWorkshop;
use Cntysoft\Kernel\ConfigProxy;
use Cntysoft\Kernel;
class Image
{
    /**
     * 水印的类型
     */
    CONST WATER_MARK_IMAGE = 1;
    CONST WATER_MARK_TEXT = 2;
    /**
     * 图片长宽的标识 0
     */
    CONST IMAGE_ZERO = 0;
    /**
     * 缩略图方法标识
     */
    CONST THUMB_METHOD_ONE = 1;
    CONST THUMB_METHOD_TWO = 2;
    CONST THUMB_METHOD_THREE = 3;
    /**
     * 关于缩略图和水印的相关配置选项
     * @var array
     */
    protected $config = null;
    /**
     * 用来保存作为水印的图片或文字的对象
     * @var \PHPImageWorkshop\ImageWorkshop
     */
    protected static $iwsWaterMark = null;
    /**
     * 需要被处理的图片
     * @var \PHPImageWorkshop\ImageWorkshop
     */
    protected $iwsImage = null;
    /**
     * 要被处理的图片的类型
     * @var string
     */
    protected $iwsImageType = null;

    /**
     * 图片处理类构造函数
     *
     * <code>
     *  array(
     *      'imageFromPath' => $path
     *  )
     * </code>
     * @param array $imagePath
     * @throws Exception
     */
    public function __construct(array $imagePath, $options = array())
    {
        $this->checkRequireFields($imagePath, array('imageFromPath'));
        if (@!($imageSizeInfos = getimagesize($imagePath['imageFromPath']))) {
            $errorType = ErrorType::getInstance();
            Kernel\throw_exception(new Exception(
                $errorType->msg('E_IMAGE_TO_DEAL_NOT_EXIT'),
                $errorType->code('E_IMAGE_TO_DEAL_NOT_EXIT')), $errorType);
        }
        $mimeContentTypes = explode("/", $imageSizeInfos["mime"]);
        $mimeContentType = $mimeContentTypes[1];

        switch ($mimeContentType) {
            case "jpeg":
                $image['imageVar'] = imagecreatefromjpeg($imagePath['imageFromPath']);
                $this->iwsImageType = '.jpg';
                break;
            case "gif":
                $image['imageVar'] = imagecreatefromgif($imagePath['imageFromPath']);
                $this->iwsImageType = '.gif';
                break;
            case "png":
                $image['imageVar'] = imagecreatefrompng($imagePath['imageFromPath']);
                $this->iwsImageType = '.png';
                break;
            default:
                $errorType = ErrorType::getInstance();
                Kernel\throw_exception(new Exception(
                    $errorType->msg('E_IMAGE_TYPE_NOT_EXIT'),
                    $errorType->code('E_IMAGE_TYPE_NOT_EXIT')), $errorType);
                break;
        }

        $this->iwsImage = new ImageWorkshop($image);
		$this->config = ConfigProxy::getFrameworkConfig('ThumbWaterMark', ConfigProxy::C_TYPE_FRAMEWORK_VENDER)->toArray();
		 $this->setOptions($this->config, $options);
    }

	/**
	 * 获取需要处理的图片的对象
	 *
	 * @return \PHPImageWorkshop\ImageWorkshop
	 */
	public function getIwsImage()
	{
		return $this->iwsImage;
	}
    
    public function getIwsImageType()
    {
        return $this->iwsImageType;
    }
    
    /**
     * 生成缩略图
     *
     * @param string $path 指定缩略图的保存路径，不包含名字
     * @param string $name 缩略图的名字，不包含类型后缀，这是为了防止随意的设置文件后缀，当后缀与原文件不同时，消耗资源太大，不支持改格式
     * @param string $position 如果缩略图是截取生成，设置截取的位置，可选参数 LT:左上, MT: 中上, RT:右上, LM: 左中, MM: 中间, RM: 右中, LB:左下, MB:中下, RB:右下， 默认LT左上
     * @param integer $quality 图片质量，只有生成jpg时有效果。默认75，可选0-100，0最差，100最好但资源消耗也最大
     * @return string 返回生成的缩略图名称
     */
    public function generateThumbnail($path, $name, $position = 'LT', $quality = 75)
    {
        $config = $this->config;
        $this->checkRequireFields($config, array('thumb'));
        $this->checkRequireFields($config['thumb'], array('width', 'height', 'method', 'backgroundColor'));
        $configWidth = $config['thumb']['width'];
        $configHeight = $config['thumb']['height'];
        $method = $config['thumb']['method'];
        $backgroundColor = $config['thumb']['backgroundColor'];
        $name = $name . $this->iwsImageType;

        if (self::IMAGE_ZERO == $configWidth && self::IMAGE_ZERO == $configHeight) {
            $filename = $this->iwsImage->save($path, $name, true, null, $quality);
            return $filename;
        }
        switch ($method) {
            case self::THUMB_METHOD_ONE:
                if (self::IMAGE_ZERO != $configWidth && self::IMAGE_ZERO != $configHeight) {
                    $this->iwsImage->resizeInPixel($configWidth, $configHeight, '', 0, 0, $position);
                    $filename = $this->iwsImage->save($path, $name, true, null, $quality);
                } else {
                    $filename = $this->thumbnailInPercent($configWidth, $configHeight, $path, $name, $position, $quality);
                }
                break;
            case self::THUMB_METHOD_TWO:
                if (self::IMAGE_ZERO != $configWidth && self::IMAGE_ZERO != $configHeight) {
                    $imageWidth = $this->iwsImage->getWidth();
                    $imageHeight = $this->iwsImage->getHeight();
                    if ($imageWidth > $imageHeight) {
                        $percent = ($configHeight / $imageHeight) * 100;
                        $this->iwsImage->resizeInPercent($percent, $percent);
                    } else {
                        $percent = ($configWidth / $imageWidth) * 100;
                        $this->iwsImage->resizeInPercent($percent, $percent);
                    }
                    $this->iwsImage->cropInPixel($configWidth, $configHeight, 0, 0, $position);
                    $filename = $this->iwsImage->save($path, $name, true, null, $quality);
                } else {
                    $filename = $this->thumbnailInPercent($configWidth, $configHeight, $path, $name, $position, $quality);
                }
                break;
            case self::THUMB_METHOD_THREE:
                if (self::IMAGE_ZERO != $configWidth && self::IMAGE_ZERO != $configHeight) {
                    $imageWidth = $this->iwsImage->getWidth();
                    $imageHeight = $this->iwsImage->getHeight();
                    if ($imageWidth < $imageHeight) {
                        $percent = ($configHeight / $imageHeight) * 100;
                        $this->iwsImage->resizeInPercent($percent, $percent);
                    } else {
                        $percent = ($configWidth / $imageWidth) * 100;
                        $this->iwsImage->resizeInPercent($percent, $percent);
                    }
                    $this->iwsImage->cropInPixel($configWidth, $configHeight, 0, 0, $position, $backgroundColor);
                    $filename = $this->iwsImage->save($path, $name, true, null, $quality);
                } else {
                    $filename = $this->thumbnailInPercent($configWidth, $configHeight, $path, $name, $position, $quality);
                }
                break;
        }
        unset($this->iwsImage);
        return $filename;
    }

    /**
     * 给图片加图片或文字水印
     * @param string $path 加过水印后的图片保存路径，没有图片名称
     * @param string $name 加过水印后图片的名字，不包含类型后缀，这是为了防止随意的设置文件后缀，当后缀与原文件不同时，消耗资源太大，不支持改格式
     * @param integer $rotate 水印图片或文字是否旋，旋转的话旋转的角度 以图片右下角为圆心顺时针旋转
     * @param integer $quality 图片质量，只有生成jpg时有效果。默认75，可选0-100，0最差，100最好
     * @return string 返回生成的图片名称
     */
    public function playWaterMark($path, $name, $rotate = 0, $quality = 75)
    {
        $config = $this->config;
        $this->checkRequireFields($config, array('waterMark'));
        $this->checkRequireFields($config['waterMark'], array('type', 'minWidth', 'minHeight'));
        $type = $config['waterMark']['type'];
        $minWidth = $config['waterMark']['minWidth'];
        $minHeight = $config['waterMark']['minHeight'];
        $imageWidth = $this->iwsImage->getWidth();
        $imageHeight = $this->iwsImage->getHeight();
        $name = $name . $this->iwsImageType;
        if($imageWidth < $minWidth || $imageHeight < $minHeight){
            return;
        }

        $waterMark = $this->getIwsWaterMark();
        if (self::WATER_MARK_IMAGE == $type) {
            $this->checkRequireFields($config['waterMark'], array('pic'));
            $this->checkRequireFields($config['waterMark']['pic'], array('opacity', 'position'));
            $waterMark->opacity($config['waterMark']['pic']['opacity']);
            $waterMark->rotate($rotate);
            $this->iwsImage->addLayerOnTop($waterMark, 0, 0, $config['waterMark']['pic']['position']);
            $filename = $this->iwsImage->save($path, $name, true, null, $quality);
        } else {
            $this->checkRequireFields($config['waterMark'], array('text'));
            $this->checkRequireFields($config['waterMark']['text'], array('opacity', 'position'));
            $waterMark->opacity($config['waterMark']['text']['opacity']);
            $waterMark->rotate($rotate);
            $this->iwsImage->addLayerOnTop($waterMark, 0, 0, $config['waterMark']['text']['position']);
            $filename = $this->iwsImage->save($path, $name, true, null, $quality);
        }
        unset($this->iwsImage);
        return $filename;
    }

    /**
     * 将图片旋转一定的角度后保存
     * @param string $path  指定缩略图的保存路径，不包含名字
     * @param string $name 生成图片的名字，不包含类型后缀，这是为了防止随意的设置文件后缀，当后缀与原文件不同时，消耗资源太大，不支持改格式
     * @param integer $rotate 旋转的角度，
     * @param string $backgroundColor 值类似：'ffffff'，旋转后空白的填充色
     * @param integer $quality  jpg图片时图片的质量
     * @return string 返回生成的图片名称
     */
    public function rotateImage($path, $name, $rotate = 0, $backgroundColor = 'ffffff', $quality = 75)
    {
        $name = $name . $this->iwsImageType;
        $this->iwsImage->rotate($rotate);
        return $this->iwsImage->save($path, $name, true, $backgroundColor, $quality);
    }

    /**
     * 当系统设置中缩略图的宽度或高度中有一项为0时，将调用这个方法
     * @param integer $configWidth 配置的缩略图宽度
     * @param integer $configHeight 配置的缩略图高度
     * @param string $path 缩略图保存路径
     * @param string $name 缩略图名字
     * @param string $position 截取的位置
     * @param integer $quality 图片质量，只有生成jpg时有效果。默认75，可选0-100，0最差，100最好
     * @return string 返回生成的图片的名称
     */
    protected function thumbnailInPercent($configWidth, $configHeight, $path, $name, $position, $quality)
    {
        if (self::IMAGE_ZERO == $configWidth) {
            $imageHeight = $this->iwsImage->getHeight();
            $percent = ($configHeight / $imageHeight) * 100;
            $this->iwsImage->resizeInPercent($percent, $percent, '', 0, 0, $position);
            return $this->iwsImage->save($path, $name, true, null, $quality);
        } else {
            $imageWidth = $this->iwsImage->getWidth();
            $percent = ($configWidth / $imageWidth) * 100;
            $this->iwsImage->resizeInPercent($percent, $percent, '', 0, 0, $position);
            return $this->iwsImage->save($path, $name, true, null, $quality);
        }
    }

    /**
     *  获取作为水印的图片或文字的对象
     * @return PHPImageWorkshop\ImageWorkshop
     */
    protected function getIwsWaterMark()
    {
        if(null == self::$iwsWaterMark){
            $config = $this->config;
            $this->checkRequireFields($config, array('waterMark'));
            if(self::WATER_MARK_IMAGE == $config['waterMark']['type']){
                $this->checkRequireFields($config['waterMark'], array('pic'));
                $this->checkRequireFields($config['waterMark']['pic'], array('imageFromPath'));
                $params['imageFromPath'] = CNTY_ROOT_DIR . DS . ltrim($config['waterMark']['pic']['imageFromPath'], '\ /');
                self::$iwsWaterMark = new ImageWorkshop($params);
            }else{
                $this->checkRequireFields($config['waterMark'], array('text'));
                $this->checkRequireFields($config['waterMark']['text'], array('text', 'fontPath', 'fontSize', 'fontColor'));
                $params['text'] = $config['waterMark']['text']['text'];
                $params['fontPath'] = $this->getFontPath() . DS . $config['waterMark']['text']['fontPath'];
                $params['fontSize'] = $config['waterMark']['text']['fontSize'];
                $params['fontColor'] = $config['waterMark']['text']['fontColor'];
                self::$iwsWaterMark = new ImageWorkshop($params);
            }
        }
        return self::$iwsWaterMark;
    }

    /**
     * 获取水印字体的路径
     * @return string
     */
    protected function getFontPath()
    {
        return CNTY_DATA_DIR . DS . 'Framework/WaterMark/Font';
    }

	public function setOptions(array &$config, array $options)
	{
		foreach($config as $key => &$value) {
			if (array_key_exists($key, $options) && is_array($value)) {
				$this->setOptions($value, $options[$key]);
			} else {
				if (array_key_exists($key, $options)) {
					$value = $options[$key];
				}
			}
		}
		unset($value);
		return $config;
	}
    /**
     * 检查是否具有必要的参数
     *
     * @throws Exception
     */
    protected function checkRequireFields(array &$params = array(), array $requires = array())
    {
        $leak = array();
        Kernel\array_has_requires($params, $requires, $leak);
        if (!empty($leak)) {
            Kernel\throw_exception(new Exception(
                Kernel\StdErrorType::msg('E_API_INVOKE_LEAK_ARGS', implode(', ', $leak)),
                Kernel\StdErrorType::code('E_API_INVOKE_LEAK_ARGS')), \Cntysoft\STD_EXCEPTION_CONTEXT);
        }
    }
}