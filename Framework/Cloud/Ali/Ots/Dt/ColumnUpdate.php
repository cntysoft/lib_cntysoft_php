<?php
/**
 * Cntysoft Cloud Software Team
 *
 * @author SOFTBOY <cntysoft@163.com>
 * @copyright  Copyright (c) 2010-2011 Cntysoft Technologies China Inc. <http://www.cntysoft.com>
 * @license    http://www.cntysoft.com/license/new-bsd     New BSD License
 */
namespace Cntysoft\Framework\Cloud\Ali\Ots\Dt;
use DrSlump\Protobuf\AnnotatedMessage;
class ColumnSchema extends AnnotatedMessage
{
   /** @protobuf(tag=1, type=enum, required, reference=OperationType) */
   public $type;
   /** @protobuf(tag=2, type=string, required) */
   public $name;
   /** @protobuf(tag=3, type=message, optional, reference=ColumnValue) */
   public $value;
}