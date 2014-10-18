<?php
/**
 * Cntysoft Cloud Software Team
 *
 * @author SOFTBOY <cntysoft@163.com>
 * @copyright  Copyright (c) 2010-2011 Cntysoft Technologies China Inc. <http://www.cntysoft.com>
 * @license    http://www.cntysoft.com/license/new-bsd     New BSD License
 */
namespace Cntysoft\Framework\Utils;
use Cntysoft\Stdlib\ErrorType as BaseErrorType;
class ErrorType extends BaseErrorType
{
    /**
     * @var array $map
     */
    protected $map = array(
        'E_AREA_MAP_FILE_NOT_EXIST' => array(10001, 'China area map file : %s is not exist'),
        'E_IMAGE_TO_DEAL_NOT_EXIT' => array(10002, 'The image you want to deal with is not exit.'),
        'E_IMAGE_PATH_NOT_EXIT' => array(10003, 'The image path is not exit.'),
        'E_IMAGE_TYPE_NOT_EXIT' => array(10004, 'Not an image file (jpeg/png/gif) at %s')
    );
}