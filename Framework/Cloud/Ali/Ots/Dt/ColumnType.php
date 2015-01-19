<?php
/**
 * Cntysoft Cloud Software Team
 *
 * @author SOFTBOY <cntysoft@163.com>
 * @copyright  Copyright (c) 2010-2011 Cntysoft Technologies China Inc. <http://www.cntysoft.com>
 * @license    http://www.cntysoft.com/license/new-bsd     New BSD License
 */
namespace Cntysoft\Framework\Cloud\Ali\Ots\Dt;
use DrSlump\Protobuf\Enum;
class ColumnType extends Enum
{
   protected $constants = array(
      'INF_MIN' => 0,
      'INF_MAX' => 1,
      'INTEGER' => 2,
      'STRING' => 3,
      'BOOLEAN' => 4,
      'DOUBLE' => 5,
      'BINARY' => 6
   );
}