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
      'E_IMAGE_TYPE_NOT_EXIT' => array(10004, 'Not an image file (jpeg/png/gif) at %s'),
      'E_WORK_ID_RANGE_ERROR' => array(10005, 'worker Id can\'t be greater than 15 or less than 0'),
      'E_CLOCL_MOVE_BACKWARD' => array(10006, 'Clock moved backwards.  Refusing to generate id for %s milliseconds"'),
      'E_QR_CODE_IMAGE_FUNCTION_IS_NOT_EXIST' => array(10007, 'QRCode: function image %s does not exists.'),
      'E_QR_CODE_DATA_IS_NOT_EXIST' => array(10008, 'QRCode: Data does not exists.'),
      'E_QR_CODE_VERSION_IS_TOO_LARGE' => array(10009, 'QRCode : version too large'),
      'E_QR_CODE_VERFLOW_ERROR' => array(10010, 'QRCode : Overflow error'),
      'E_QR_CODE_IMAGE_SIZE_IS_TOO_LARGE' => array(10011, 'QRCode : Image size too large')
   );
}