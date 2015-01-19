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
class Row extends AnnotatedMessage
{
   /** @protobuf(tag=1, type=message, repeated, refrence=Column) */
   public $primary_key_columns = array();
   /** @protobuf(tag=2, type=message, repeated, refrence=Column) */
   public $attribute_columns = array();

}