<?php
/**
 * Cntysoft Cloud Software Team
 */
namespace Cntysoft\Framework\Cloud\Ali\OpenSearch;
use Cntysoft\Stdlib\ErrorType as BaseErrorType;
 /**
 * @author SOFTBOY <cntysoft@163.com>
 * @copyright  Copyright (c) 2010-2011 Cntysoft Technologies China Inc. <http://www.cntysoft.com>
 * @license    http://www.cntysoft.com/license/new-bsd     New BSD License
 */
class ErrorType extends BaseErrorType
{
   protected $map = array(
      'DOC_FORMAT_ERROR' => array(10001, 'Operation failed. The docs is not correct.'),
      'REQUEST_ERROR' => array(10002, 'request aliyun network error : %s')
   );
}