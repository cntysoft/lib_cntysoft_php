<?php
/**
 * Cntysoft Cloud Software Team
 *
 * @author SOFTBOY <cntysoft@163.com>
 * @copyright  Copyright (c) 2010-2011 Cntysoft Technologies China Inc. <http://www.cntysoft.com>
 * @license    http://www.cntysoft.com/license/new-bsd     New BSD License
 */
namespace Cntysoft\Framework\Utils;
use Cntysoft\Kernel;
/**
 * 认证码图片生成程序
 * 
 * 此程序来自THINKPHP框架，非我们原创，版权ThinkPHP所有，我们只是改写让其符合我们的框架
 */
class CheckCode
{
    /**
     * 验证码过期时间（s）
     * 
     * @var int $expire
     */
    protected $expire = 30;
    /**
     * 使用背景图片
     *
     * @var boolean $useImgBg
     */
    protected $useImgBg = false;
    /**
     * 验证码字体大小（px）
     * 
     * @var int $fontSize
     */
    protected $fontSize = 25;
    /**
     * 是否画混淆曲线
     *
     * @var boolean $useCurve
     */
    protected $useCurve = true;
    /**
     * 是否添加杂点
     * 
     * @var boolean $useNoise
     */
    protected $useNoise = true;
    /**
     * 验证码图片宽
     *
     * @var int $imageH
     */
    protected $imageH = 0;
    /**
     *  验证码图片长
     *
     * @var int $imageL
     */
    protected $imageL = 0;
    /**
     * 验证码位数
     *
     * @var int $length
     */
    protected $length = 5;
    /**
     * 验证码字体，不设置随机获取
     *
     * @var string $fontttf
     */
    protected $fontttf = '4.ttf';
    /**
     * 验证码背景图片颜色
     * 
     * @var array $bg
     */
    protected $bg = array(243, 251, 254);
    /**
     * 验证码中使用的字符，01IO容易混淆，建议不用
     *
     * @var string $codeSet
     */
    private $codeSet = '2345678abcdefhijkmnpqrstuvwxyzABCDEFGHJKLMNPQRTUVWXYZ';
    /**
     * 验证码图片实例
     *
     * @var null $image
     */
    private $image = NULL;
    /**
     * 验证码字体颜色
     *
     * @var null $color
     */
    private $color = NULL;
    /**
     * 存入Session的键值
     * 
     * @var null | string $sessionKey
     */
    private $sessionKey = null;

    /**
     * 构造函数
     * 
     * @param int $fontSize  验证码的字体大小
     * @param int $length  验证码的长度
     */
    public function __construct($sessionKey, $fontSize = null, $length = null)
    {
        if(!is_scalar($sessionKey)) {
            $errorType = new ErrorType();
            throw new \Exception($errorType->msg('INVALID_SESSION_KEY'), $errorType->code('INVALID_SESSION_KEY'));
        }else {
            $this->sessionKey = $sessionKey;
        }
        
        if (null != $fontSize) {
            $this->fontSize = $fontSize;
        }
        if (null != $length) {
            $this->length = $length;
        }
    }

    /**
     * 输出验证码并把验证码的值保存的session中
     * 验证码保存到session的格式为： array('code' => '验证码值', 'time' => '验证码创建时间');
     */
    public function draw()
    {

        // 图片宽(px)
        $this->imageL || $this->imageL = $this->length * $this->fontSize * 1.5 + $this->length * $this->fontSize / 2;
        // 图片高(px)
        $this->imageH || $this->imageH = $this->fontSize * 2.5;
        // 建立一幅 $this->imageL x $this->imageH 的图像
        $this->image = imagecreate($this->imageL, $this->imageH);
        // 设置背景      
        imagecolorallocate($this->image, $this->bg[0], $this->bg[1], $this->bg[2]);

        // 验证码字体随机颜色
        $this->color = imagecolorallocate($this->image, mt_rand(1, 150), mt_rand(1, 150), mt_rand(1, 150));
        // 验证码使用随机字体
        $ttfPath = Kernel\StdDir::getDataDir() . DS .'Framework' . DS . 'Utils' . DS . 'CheckCode' . DS . 'Ttfs' . DS;

        if (empty($this->fontttf)) {
            $dir = dir($ttfPath);
            $ttfs = array();
            while (false !== ($file = $dir->read())) {
                if ($file[0] != '.' && substr($file, -4) == '.ttf') {
                    $ttfs[] = $file;
                }
            }
            $dir->close();
            $this->fontttf = $ttfs[array_rand($ttfs)];
        }
        $this->fontttf = $ttfPath . $this->fontttf;

        if ($this->useImgBg) {
            $this->background();
        }

        if ($this->useNoise) {
            // 绘杂点
            $this->writeNoise();
        }
        if ($this->useCurve) {
            // 绘干扰线
            $this->writeCurve();
        }

        // 绘验证码
        $code = array(); // 验证码
        $codeNX = 0; // 验证码第N个字符的左边距
        for ($i = 0; $i < $this->length; $i++) {
            $code[$i] = $this->codeSet[mt_rand(0, 51)];
            $codeNX += mt_rand($this->fontSize * 1.2, $this->fontSize * 1.6);
            imagettftext($this->image, $this->fontSize, mt_rand(-40, 40), $codeNX, $this->fontSize*1.6, $this->color, $this->fontttf, $code[$i]);
        }
        
        //加密验证码
        $code = strtoupper(implode('', $code));
        //保存验证码到Session
        $this->setupSession($code);

        header('Cache-Control: private, max-age=0, no-store, no-cache, must-revalidate');
        header('Cache-Control: post-check=0, pre-check=0', false);
        header('Pragma: no-cache');
        header("content-type: image/png");

        // 输出图像
        imagepng($this->image);
        //删除图像
        imagedestroy($this->image);
    }

    /**
     * 画一条由两条连在一起构成的随机正弦函数曲线作干扰线(你可以改成更帅的曲线函数) 
     *      
     *      高中的数学公式咋都忘了涅，写出来
     * 		正弦型函数解析式：y=Asin(ωx+φ)+b
     *      各常数值对函数图像的影响：
     *        A：决定峰值（即纵向拉伸压缩的倍数）
     *        b：表示波形在Y轴的位置关系或纵向移动距离（上加下减）
     *        φ：决定波形与X轴位置关系或横向移动距离（左加右减）
     *        ω：决定周期（最小正周期T=2π/∣ω∣）
     *
     */
    private function writeCurve()
    {
        $px = $py = 0;

        // 曲线前部分
        $A = mt_rand(1, $this->imageH / 2);                  // 振幅
        $b = mt_rand(-$this->imageH / 4, $this->imageH / 4);   // Y轴方向偏移量
        $f = mt_rand(-$this->imageH / 4, $this->imageH / 4);   // X轴方向偏移量
        $T = mt_rand($this->imageH, $this->imageL * 2);  // 周期
        $w = (2 * M_PI) / $T;

        $px1 = 0;  // 曲线横坐标起始位置
        $px2 = mt_rand($this->imageL / 2, $this->imageL * 0.8);  // 曲线横坐标结束位置

        for ($px = $px1; $px <= $px2; $px = $px + 1) {
            if ($w != 0) {
                $py = $A * sin($w * $px + $f) + $b + $this->imageH / 2;  // y = Asin(ωx+φ) + b
                $i = (int) ($this->fontSize / 5);
                while ($i > 0) {
                    imagesetpixel($this->image, $px + $i, $py + $i, $this->color);  // 这里(while)循环画像素点比imagettftext和imagestring用字体大小一次画出（不用这while循环）性能要好很多				
                    $i--;
                }
            }
        }

        // 曲线后部分
        $A = mt_rand(1, $this->imageH / 2);                  // 振幅		
        $f = mt_rand(-$this->imageH / 4, $this->imageH / 4);   // X轴方向偏移量
        $T = mt_rand($this->imageH, $this->imageL * 2);  // 周期
        $w = (2 * M_PI) / $T;
        $b = $py - $A * sin($w * $px + $f) - $this->imageH / 2;
        $px1 = $px2;
        $px2 = $this->imageL;

        for ($px = $px1; $px <= $px2; $px = $px + 1) {
            if ($w != 0) {
                $py = $A * sin($w * $px + $f) + $b + $this->imageH / 2;  // y = Asin(ωx+φ) + b
                $i = (int) ($this->fontSize / 5);
                while ($i > 0) {
                    imagesetpixel($this->image, $px + $i, $py + $i, $this->color);
                    $i--;
                }
            }
        }
    }

    /**
     * 画杂点
     * 往图片上写不同颜色的字母或数字
     */
    private function writeNoise()
    {
        for ($i = 0; $i < 10; $i++) {
            //杂点颜色
            $noiseColor = imagecolorallocate($this->image, mt_rand(150, 225), mt_rand(150, 225), mt_rand(150, 225));
            for ($j = 0; $j < 5; $j++) {
                // 绘杂点
                imagestring($this->image, 5, mt_rand(-10, $this->imageL), mt_rand(-10, $this->imageH), $this->codeSet[mt_rand(0, 27)], $noiseColor);
            }
        }
    }

    /**
     * 绘制背景图片
     * 注：如果验证码输出图片比较大，将占用比较多的系统资源
     */
    private function background()
    {
        $path = Kernel\StdDir::getDataDir() . DS .'Framework' . DS .'Security' . DS . 'Verify' . DS . 'Bgs' . DS;
        $dir = dir($path);

        $bgs = array();
        while (false !== ($file = $dir->read())) {
            if ($file[0] != '.' && substr($file, -4) == '.jpg') {
                $bgs[] = $path . $file;
            }
        }
        $dir->close();

        $gb = $bgs[array_rand($bgs)];

        list($width, $height) = @getimagesize($gb);
        // Resample
        $bgImage = @imagecreatefromjpeg($gb);
        @imagecopyresampled($this->image, $bgImage, 0, 0, 0, 0, $this->imageL, $this->imageH, $width, $height);
        @imagedestroy($bgImage);
    }

    /**
     * 将验证码信息写入Session
     * 
     * @param string $code
     */
    private function setupSession($code) 
    {
        $sessionMgr = Kernel\get_global_di()->getShared('SessionManager');

        $sessionMgr->setExpirationSeconds($this->expire, array(
           $this->sessionKey
        ));
        $sessionMgr->offsetSet($this->sessionKey, $code);
    }
}
