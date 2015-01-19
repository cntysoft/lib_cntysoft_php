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
class RowExistenceExpectation extends Enum
{
   protected $constants = array(
      'IGNORE' => 0,
      'EXPECT_EXIST' => 1,
      'EXPECT_NOT_EXIST' => 2
   );
}