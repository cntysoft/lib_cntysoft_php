<?php
/**
 * Cntysoft Cloud Software Team
 *
 * @author SOFTBOY <cntysoft@163.com>
 * @copyright  Copyright (c) 2010-2011 Cntysoft Technologies China Inc. <http://www.cntysoft.com>
 * @license    http://www.cntysoft.com/license/new-bsd     New BSD License
 */
use DrSlump\Protobuf\AnnotatedMessage;
class TableInBatchWriteRowResponse extends AnnotatedMessage
{
   /** @protobuf(tag=1, type=string, required) */
   public $table_name;
   /** @protobuf(tag=2, type=message, repeated, refrence=PutRowInBatchWriteRowResponse) */
   public $put_rows = array();
   /** @protobuf(tag=3, type=message, repeated, refrence=UpdateRowInBatchWriteRowResponse) */
   public $update_rows = array();
   /** @protobuf(tag=4, type=message, repeated, refrence=DeleteRowInBatchWriteRowResponse) */
   public $delete_rows = array();
}