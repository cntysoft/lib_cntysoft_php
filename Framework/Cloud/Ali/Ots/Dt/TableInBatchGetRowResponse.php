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
class TableInBatchGetRowResponse extends AnnotatedMessage
{
   /** @protobuf(tag=1, type=string, required) */
   public $table_name;
   /** @protobuf(tag=2, type=message, repeated, refrence=RowInBatchGetRowResponse) */
   public $rows = array();
}