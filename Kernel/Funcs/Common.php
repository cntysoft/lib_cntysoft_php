<?php
/**
 * Cntysoft Cloud Software Team
 * 
 * @author SOFTBOY <cntysoft@163.com>
 * @copyright  Copyright (c) 2010-2011 Cntysoft Technologies China Inc. <http://www.cntysoft.com>
 * @license    http://www.cntysoft.com/license/new-bsd     New BSD License
 */
namespace Cntysoft\Kernel;
use Cntysoft\Phalcon\Mvc\Application;
use Cntysoft\Stdlib\Filesystem;
/**
 * 注销数组指定的key关联的数据
 * 
 * @param array $data
 * @param array $fields 需要unset的数组的键值集合
 */
function unset_array_values(array &$data, array $fields)
{
    foreach ($fields as $key) {
        if (array_key_exists($key, $data)) {
            unset($data[$key]);
        }
    }
}

/**
 * 判断数据是否含有必须的字段
 * 
 * @param array &$data
 * @param array $requires
 * @return boolean
 */
function array_has_requires(array &$data, array $requires, array &$leak = array())
{
    $isOk = true;
    foreach ($requires as $key) {
        if (!array_key_exists($key, $data)) {
            $leak[] = $key;
            $isOk = false;
        }
    }
    return $isOk;
}

/**
 * 保证数组具有必须的字段
 * 
 * @param array &$data
 * @param array &$requires
 * @param string $throwMsg 抛出异常的提示字符串
 */
function ensure_array_has_fields(array &$data, array $requires, $throwMsg = null, $errorCode = null)
{

    $leak = array();
    if (!array_has_requires($data, $requires, $leak)) {
        if (!$throwMsg) {
            $throwMsg = StdErrorType::msg('E_ARRAY_KEYS_NOT_EXIST', implode(', ', $leak));
        } else {
            $throwMsg = sprintf($throwMsg, implode(', ', $leak));
        }
        if (!$errorCode) {
            $errorCode = StdErrorType::code('E_ARRAY_KEYS_NOT_EXIST');
        }
        throw new Exception($throwMsg, $errorCode);
    }
}

/**
 * 获取全局的对象管理器
 * 
 * @return \Phalcon\DI 
 */
function get_global_di()
{
    static $di = null;
    if (null == $di) {
        $di = Application::getGlobalDi();
    }
    return $di;
}

/**
 * @return \Phalcon\Db\Adapter\Pdo\Mysql
 */
function get_db_adapter()
{
    $di = get_global_di();
    return $di->getShared('db');
}

/**
 * 获取全局事务管理器
 * 
 * @return \Phalcon\Mvc\Model\Transaction\Manager
 */
function get_transaction_manager()
{
    $di = get_global_di();
    return $di->getShared('transactionManager');
}

/**
 * 获取ORM模型管理器
 * 
 * @return \Phalcon\Mvc\Model\Manager
 */
function get_models_manager()
{
    static $manager = null;
    if (null == $manager) {
        $di = get_global_di();
        $manager = $di->getShared('modelsManager');
    }
    return $manager;
}

/**
 * 获取标准的系统路径
 * 
 * @param string $path
 * @return string
 */
function real_path($path)
{
    //去掉多余的斜杠
    $len = strlen($path);
    if (0 == $len) {
        return '';
    }
    $current = '';
    $filtered = '';
    $readedDp = false;
    for ($i = 0; $i < $len; $i++) {
        $current = $path[$i];
        if ('/' !== $current && '\\' !== $current) {
            $readedDp = false;
        }
        if (!$readedDp) {
            if ('/' == $current || '\\' == $current) {
                $current = DS;
            }
            $filtered .= $current;
            if ('/' == $current || '\\' == $current) {
                $readedDp = true;
            }
        }
    }
    $len = strlen($filtered);
    $lastChar = $filtered[$len - 1];
    if ($lastChar == '\\' || $lastChar == '/') {
        $filtered = substr($filtered, 0, $len - 1);
    }
    $path = $filtered;
    if ((PHP_OS == \Cntysoft\WINDOWS) && is_utf8($path)) { //加了判断，只有编码为UTF-8的时候才会转变编码
        $path = iconv('UTF-8', 'GBK', $path);
    }
    return $path;
}

/**
 * 判断一个字符串是否为UTF8
 * 
 * @param string $string
 * @return Boolean
 */
function is_utf8($string)
{
    if (preg_match("/^([".chr(228)."-".chr(233)."]{1}[".chr(128)."-".chr(191)."]{1}[".chr(128)."-".chr(191)."]{1}){1}/", $string) == true || preg_match("/([".chr(228)."-".chr(233)."]{1}[".chr(128)."-".chr(191)."]{1}[".chr(128)."-".chr(191)."]{1}){1}$/", $string) == true || preg_match("/([".chr(228)."-".chr(233)."]{1}[".chr(128)."-".chr(191)."]{1}[".chr(128)."-".chr(191)."]{1}){2,}/", $string) == true) {
        return true;
    } else {
        return false;
    }
}

/**
 * 过滤传入路径中的根路径
 * 
 * @param string $path
 * @return string
 */
function filter_root_dir($path)
{
    return str_replace(CNTY_ROOT_DIR, '', $path);
}

/**
 * 暂时只支持文件缓存
 * 
 * @param string $dir 子缓存目录 CNTY_DATA_DIR.DS.'Cache' 开始
 * @param int $lifetime
 * @return \Phalcon\Cache\Backend\File
 */
function make_cache_object($dir = null, $lifetime = 3600)
{
    $frontCache = new \Phalcon\Cache\Frontend\Data(array(
       "lifetime" => $lifetime
    ));
    $cacheDir = CNTY_DATA_DIR.DS.'Cache';
    if ($dir) {
        $cacheDir .= DS.$dir.DS.  get_church_id();
    }
    if (!file_exists($cacheDir)) {
        Filesystem::createDir($cacheDir, 0755, true);
    }
    return new \Phalcon\Cache\Backend\File($frontCache, array(
       "cacheDir" => $cacheDir.DS
    ));
}

/**
 * 快速调用对象里面的方法
 * 
 * @param stdObject $handler
 * @param string $fn           
 * @param array $params           
 * @return mixed
 */
function call_fn_with_params($handler, $fn, array $params = array())
{
    // 提供5个参数的快捷调用
    switch (count($params)) {
        case 0:
            return $handler->$fn();
            break;
        case 1:
            return $handler->$fn($params[0]);
            break;
        case 2:
            return $handler->$fn($params[0], $params[1]);
            break;
        case 3:
            return $handler->$fn($params[0], $params[1], $params[2]);
            break;
        case 4:
            return $handler->$fn($params[0], $params[1], $params[2], $params[3]);
            break;
        case 5:
            return $handler->$fn($params[0], $params[1], $params[2], $params[3], $params[4]);
            break;
        default:
            return \call_user_func_array(array($handler, $fn), $params);
            break;
    }
}

/**
 * 调用对象的Setter方法
 * 
 * @param Object $object
 * @param array $data
 * @return Object
 */
function invoke_setter($object, array $data)
{
    foreach ($data as $key => $value) {
        $setter = 'set'. ucfirst($key);
        if (!method_exists($object, $setter)) {
            throw_exception(new Exception(
                    StdErrorType::msg('E_METHOD_NOT_EXIST', get_class($object), $setter)), \Cntysoft\STD_EXCEPTION_CONTEXT);
        }
        $object->{$setter}($value);
    }
    return $object;
}

/**
 * 获取客户端IP地址
 * 
 * @return string
 */
function get_client_ip()
{
    static $ip = null;
    if ($ip !== null)
        return $ip;
    if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        $pos = array_search('unknown', $arr);
        if (false !== $pos)
            unset($arr[$pos]);
        $ip = trim($arr[0]);
    }elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (isset($_SERVER['REMOTE_ADDR'])) {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    // IP地址合法验证
    $ip = (false !== ip2long($ip)) ? $ip : '0.0.0.0';
    return $ip;
}

/**
 * 格式化DateTime对象，获取其字符串表示
 * 
 * @param \DateTime $date
 * @param string $format
 */
function get_date_string(\DateTime $date = null, $format = \Cntysoft\STD_DATE_FORMAT)
{
    return $date ? $date->format($format) : '';
}
/**
 * 将timestamp转换成字符串的形式
 * 
 * @param int $timestamp
 * @return string
 */
function format_timestamp($timestamp)
{
    return date(\Cntysoft\STD_DATE_FORMAT, $timestamp);
}
/**
 * 获取服务器相关信息
 * 
 * @param string $key
 * @return string
 */
function get_server_env($key)
{
    if (!is_string($key)) {
        throw new Exception(
        'env key must be the type of string'
        );
    }
    if ('HTTPS' == $key) {
        if (isset($_SERVER['HTTPS'])) {
            return (!empty($_SERVER['HTTPS']) && 'off' != $_SERVER['HTTPS']);
        }
        return (0 === strrpos(get_server_env('SCRIPT_URI'), 'https://'));
    }
    if ('SCRIPT_NAME' == $key) {
        if (get_server_env('CGI_MODE') && isset($_ENV['SCRIPT_URL'])) {
            $key = 'SCRIPT_URL';
        }
    }
    $value = null;
    if (isset($_SERVER[$key])) {
        $value = $_SERVER[$key];
    } elseif (isset($_ENV[$key])) {
        $value = $_ENV[$key];
    } elseif (getenv($key) !== false) {
        $value = getenv($key);
    }
    if ($key === 'REMOTE_ADDR' && $value === get_server_env('SERVER_ADDR')) {
        $addr = get_server_env('HTTP_PC_REMOTE_ADDR');
        if ($addr !== null) {
            $value = $addr;
        }
    }

    if (null !== $value) {
        return $value;
    }
    switch ($key) {
        case 'SCRIPT_FILENAME':
            if (defined('SERVER_IIS') && SERVER_IIS === true) {
                return str_replace('\\\\', '\\', get_server_env('PATH_TRANSLATED'));
            }
            break;
        case 'DOCUMENT_ROOT':
            $name = get_server_env('SCRIPT_NAME');
            $filename = get_server_env('SCRIPT_FILENAME');
            $offset = 0;
            if (!strpos($name, '.php')) {
                $offset = 4;
            }
            return substr($filename, 0, -(strlen($name) + $offset));
            break;
        case 'PHP_SELF':
            return str_replace(get_server_env('DOCUMENT_ROOT'), '', get_server_env('SCRIPT_FILENAME'));
            break;
        case 'CGI_MODE':
            return (PHP_SAPI === 'cgi');
            break;
        case 'HTTP_BASE':
            $host = get_server_env('HTTP_HOST');
            $parts = explode('.', $host);
            $count = count($parts);
            if ($count === 1) {
                return '.'.$host;
            } elseif ($count === 2) {
                return '.'.$host;
            } elseif ($count === 3) {
                $gTLD = array(
                   'aero',
                   'asia',
                   'biz',
                   'cat',
                   'com',
                   'coop',
                   'edu',
                   'gov',
                   'info',
                   'int',
                   'jobs',
                   'mil',
                   'mobi',
                   'museum',
                   'name',
                   'net',
                   'org',
                   'pro',
                   'tel',
                   'travel',
                   'xxx'
                );
                if (in_array($parts[1], $gTLD)) {
                    return '.'.$host;
                }
            }
            array_shift($parts);
            return '.'.implode('.', $parts);
            break;
    }
    return null;
}

/**
 * 获取一个简便的版本字符串
 * 
 * @return string
 */
function get_sys_version_str()
{
    return 'v'.OPEN_ENGINE_VERSION.'-'.OPEN_ENGINE_RELEASE;
}

/**
 * 移除xss跨站攻击代码
 * 
 * @param string $val
 * @return mixed
 */
function remove_xss($val)
{
    // remove all non-printable characters. CR(0a) and LF(0b) and TAB(9) are allowed  
    // this prevents some character re-spacing such as <java\0script>  
    // note that you have to handle splits with \n, \r, and \t later since they *are* allowed in some inputs  
    $val = preg_replace('/([\x00-\x08,\x0b-\x0c,\x0e-\x19])/', '', $val);

    // straight replacements, the user should never need these since they're normal characters  
    // this prevents like <IMG SRC=@avascript:alert('XSS')>  
    $search = 'abcdefghijklmnopqrstuvwxyz';
    $search .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $search .= '1234567890!@#$%^&*()';
    $search .= '~`";:?+/={}[]-_|\'\\';
    for ($i = 0; $i < strlen($search); $i++) {
        // ;? matches the ;, which is optional 
        // 0{0,7} matches any padded zeros, which are optional and go up to 8 chars 
        // @ @ search for the hex values 
        $val = preg_replace('/(&#[xX]0{0,8}'.dechex(ord($search[$i])).';?)/i', $search[$i], $val); // with a ;
        // @ @ 0{0,7} matches '0' zero to seven times  
        $val = preg_replace('/(�{0,8}'.ord($search[$i]).';?)/', $search[$i], $val); // with a ;
    }

    // now the only remaining whitespace attacks are \t, \n, and \r 
    $ra1 = Array('javascript', 'vbscript', 'expression', 'applet', 'meta', 'xml', 'blink', 'link', /* 'style', */ 'script', 'embed', 'object', 'iframe', 'frame', 'frameset', 'ilayer', 'layer', 'bgsound', 'title', 'base');
    $ra2 = Array('onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterchange', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload');
    $ra = array_merge($ra1, $ra2);

    $found = true; // keep replacing as long as the previous round replaced something 
    while ($found == true) {
        $val_before = $val;
        for ($i = 0; $i < sizeof($ra); $i++) {
            $pattern = '/';
            for ($j = 0; $j < strlen($ra[$i]); $j++) {
                if ($j > 0) {
                    $pattern .= '(';
                    $pattern .= '(&#[xX]0{0,8}([9ab]);)';
                    $pattern .= '|';
                    $pattern .= '|(�{0,8}([9|10|13]);)';
                    $pattern .= ')*';
                }
                $pattern .= $ra[$i][$j];
            }
            $pattern .= '/i';
            $replacement = substr($ra[$i], 0, 2).'<x>'.substr($ra[$i], 2); // add in <> to nerf the tag
            $val = preg_replace($pattern, $replacement, $val); // filter out the hex tags  
            if ($val_before == $val) {
                // no replacements were made, so exit the loop  
                $found = false;
            }
        }
    }
    return $val;
}

/**
 * 获取分页参数
 * 
 * @param array &$orderBy
 * @param string &$limit
 * @param string &$offset
 * @param array &$params
 */
function set_page_var(&$orderBy, &$limit, &$offset, &$params)
{
    $orderBy = array_key_exists('orderBy', $params) ? $params['orderBy'] : null;
    $limit = array_key_exists('limit', $params) ? (int) $params['limit'] : \Cntysoft\STD_PAGE_SIZE;
    $offset = array_key_exists('start', $params) ? (int) $params['start'] : 0;
}

/**
 * 将字符串的编码转换成UTF8
 * 
 * @param string $string
 * @return string
 */
function convert_2_utf8($string)
{
    if (PHP_OS == \Cntysoft\WINDOWS && !is_utf8($string)) { //加了判断，这个判断不是很好
        $string = iconv('GBK', 'UTF-8', $string);
    }

    return $string;
}

/**
 * 格式化大小 来自ThinkPHP框架
 * 
 * @param int $size
 * @param int $dec
 * @return string
 */
function byte_format($size, $dec = 2)
{
    $a = array("B", "KB", "MB", "GB", "TB", "PB");
    $pos = 0;
    while ($size >= 1024) {
        $size /= 1024;
        $pos++;
    }
    return round($size, $dec)." ".$a[$pos];
}

/**
 * 通过路径获取Phalcon配置对象
 * 
 * @param \Phalcon\Config $config
 * @param string $path
 * @return mixied
 */
function get_config_item_by_path($config, $path)
{
    $path = trim($path);
    if ('' == $path) {
        return $config;
    }
    $parts = explode('.', $path);
    $root = $config;
    foreach ($parts as $key) {
        if(isset($root->{$key})){
            $root = $root->{$key};
            if(!$root instanceof \Phalcon\Config){
                return $root;
            }
            continue;
        }else{
            return null;
        }
    }
    return $root;
}

/**
 * 通过路径获取Phalcon配置对象
 * 
 * @param \Phalcon\Config $configz
 * @param string $path
 * @param mixed $value 需要设置的值
 * @return \Phalcon\Config 
 */
function set_config_item_by_path($config, $path, $value)
{
    $path = trim($path);
    if ('' == $path) {
        return $config;
    }
    $parts = explode('.', $path);
    $setterKey = array_pop($parts);
    $root = $config;
    foreach ($parts as $key) {
        if(isset($root->{$key})){
            $root = $root->{$key};
        }else{
            //中途不存在直接返回 什么也不做
            return $config;
        }
    }
    $root->{$setterKey} = $value; 
    return $config;
}
/**
 * 将数组信息写入指定的文件
 * 
 * @param string $filename
 * @param array $data
 */
function write_array_to_file($fileame, array $data)
{
    if (empty($data)) {
        return 0;
    }
    $data = "<?php \nreturn ".var_export($data, true).';';
    return Filesystem::save($fileame, $data);
}
/**
 * 获取指定文件名称的扩展名称
 * pathinfo函数在处理 xxx.tar.gz这样的名称的时候有错误
 * 
 * @param string $filename
 * @return string
 */
function get_file_ext($filename)
{
    $pos = strpos($filename, '.');
    if(!$pos){
        return '';
    }
    return substr($filename, $pos + 1);
}
/**
 * 生成加密密码
 * 
 * @param string $password
 * @return string
 */
function generate_password($password)
{
    
    $encrypt = get_global_di()->getShared('security');
    return $encrypt->hash(hash('sha256', $password));
}