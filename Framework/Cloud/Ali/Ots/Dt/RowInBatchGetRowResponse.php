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
class RowInBatchGetRowResponse extends AnnotatedMessage
{
   /** @protobuf(tag=1, type=bool, required, default=true) */
   public $is_ok = true;
   /** @protobuf(tag=2, type=message, optional, refrence=Error) */
   public $error;
   /** @protobuf(tag=3, type=message, optional, refrence=ConsumedCapacity) */
   public $consumed;
   /** @protobuf(tag=4, type=message, optional, refrence=Row) */
   public $row;
}