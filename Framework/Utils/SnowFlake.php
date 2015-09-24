<?php
/**
 * Cntysoft Cloud Software Team
 * 
 * @author SOFTBOY <cntysoft@163.com>
 * @copyright  Copyright (c) 2010-2011 Cntysoft Technologies China Inc. <http://www.cntysoft.com>
 * @license    http://www.cntysoft.com/license/new-bsd     New BSD License
 */
namespace Cntysoft\Framework\Utils;
use Cntysoft\Kernel;
/**
 * 自增ID生成类
 */
class SnowFlake
{
   protected static $workerId;
   protected static $twepoch = 1361775855078;
   protected static $sequence = 0;
   protected static $maxWorkerId = 15;
   protected static $workerIdShift = 10;
   protected static $timestampLeftShift = 14;
   protected static $sequenceMask = 1023;
   protected static $lastTimestamp = -1;

   public function __construct($workId)
   {
      if ($workId > self::$maxWorkerId || $workId < 0) {
         $errorType = ErrorType::getInstance();
         Kernel\throw_exception(new Exception($errorType->msg('E_WORK_ID_RANGE_ERROR'),$errorType->code('E_WORK_ID_RANGE_ERROR')));
      }
      self::$workerId = $workId;
   }

   public function timeGen()
   {
      //获得当前时间戳
      $time = explode(' ', microtime());
      $time2 = substr($time[0], 2, 3);
      $timestramp = $time[1] . $time2;
      return $time[1] . $time2;
   }

   public function tilNextMillis($lastTimestamp)
   {
      $timestamp = $this->timeGen();
      while ($timestamp <= $lastTimestamp) {
         $timestamp = $this->timeGen();
      }
      return $timestamp;
   }

   public function nextId()
   {
      $timestamp = $this->timeGen();
      if (self::$lastTimestamp == $timestamp) {
         self::$sequence = (self::$sequence + 1) & self::$sequenceMask;
         if (self::$sequence == 0) {
            $timestamp = $this->tilNextMillis(self::$lastTimestamp);
         }
      } else {
         self::$sequence = 0;
      }
      if ($timestamp < self::$lastTimestamp) {
         $errorType = ErrorType::getInstance();
         Kernel\throw_exception(new Exception(
            $errorType->msg('E_CLOCL_MOVE_BACKWARD', self::$lastTimestamp - $timestamp),
            $errorType->code('E_CLOCL_MOVE_BACKWARD')));
      }
      self::$lastTimestamp = $timestamp;
      $nextId = ((sprintf('%.0f', $timestamp) - sprintf('%.0f', self::$twepoch) )) | ( self::$workerId << self::$workerIdShift ) | self::$sequence;
      return $nextId;
   }

}