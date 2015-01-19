<?php
/**
 * Cntysoft Cloud Software Team
 *
 * @author SOFTBOY <cntysoft@163.com>
 * @copyright  Copyright (c) 2010-2011 Cntysoft Technologies China Inc. <http://www.cntysoft.com>
 * @license    http://www.cntysoft.com/license/new-bsd     New BSD License
 */
use DrSlump\Protobuf\AnnotatedMessage;
class UpdateRowInBatchWriteRowRequest extends AnnotatedMessage
{
   /** @protobuf(tag=1, type=message, required, reference=Condition) */
   public $table_name;
   /** @protobuf(tag=2, type=message, repeated, reference=Column) */
   public $primary_key = array();
   /** @protobuf(tag=3, type=message, repeated, reference=ColumnUpdate) */
   public $attribute_columns = array();
}