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
class ReservedThroughputDetails extends AnnotatedMessage
{
   /** @protobuf(tag=1, type=message, required, refrence=CapacityUnit) */
   public $capacity_unit;
   /** @protobuf(tag=2, type=int64, required) */
   public $last_increase_time;
   /** @protobuf(tag=3, type=int64, optional) */
   public $last_decrease_time;
   /** @protobuf(tag=4, type=int32, required) */
   public $number_of_descease_today;
}