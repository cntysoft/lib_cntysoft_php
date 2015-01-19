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
class ColumnValue extends AnnotatedMessage
{
   /** @protobuf(tag=1, type=enum, required, refrence=ColumnType) */
   public $type;
   /** @protobuf(tag=2, type=int64, optional) */
   public $v_int;
   /** @protobuf(tag=3, type=string, optional) */
   public $v_string;
   /** @protobuf(tag=4, type=bool, optional) */
   public $v_bool;
   /** @protobuf(tag=5, type=double, optional) */
   public $v_double;
   /** @protobuf(tag=6, type=bytes, optional) */
   public $v_binary;
}