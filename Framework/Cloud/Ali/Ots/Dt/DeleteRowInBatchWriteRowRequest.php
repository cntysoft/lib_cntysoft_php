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
class DeleteRowInBatchWriteRowRequest extends AnnotatedMessage
{
   /** @protobuf(tag=1, type=message, required, refrence=Condition) */
   public $condition;
   /** @protobuf(tag=2, type=message, repeated, refrence=Column) */
   public $primary_key = array();
}