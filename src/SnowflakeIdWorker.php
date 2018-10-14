<?php
// 生成全局唯一ID的PHP实现类
// +----------------------------------------------------------------------
// | PHP version 5.4+
// +----------------------------------------------------------------------
// | Copyright (c) 2012-2014 http://www.17php.cn, All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhujinkui <developer@zhujinkui.com>
// +----------------------------------------------------------------------

namespace think;

class SnowflakeIdWorker
{
    /** 开始时间截 (2015-01-01) */
    private $twepoch                    = null;
    /** 机器id所占的位数 */
    private $workerIdBits               = null;
    /** 数据标识id所占的位数 */
    private $datacenterIdBits           = null;
    /** 支持的最大机器id，结果是31 (这个移位算法可以很快的计算出几位二进制数所能表示的最大十进制数) */
    private $maxWorkerId                = null;
    /** 支持的最大数据标识id，结果是31 */
    private $maxDatacenterId            = null;
    /** 序列在id中占的位数 */
    private $sequenceBits               = null;
    /** 机器ID向左移12位 */
    private $workerIdShift              = null;
    /** 数据标识id向左移17位(12+5) */
    private $datacenterIdShift          =null;
    /** 时间截向左移22位(5+5+12) */
    private $timestampLeftShift         = null;
    /** 生成序列的掩码，这里为4095 (0b111111111111 =0xfff=4095) */
    private $sequenceMask               = null;
    /** 工作机器ID(0~31) */
    private $workerId                   = null;
    /** 数据中心ID(0~31) */
    private $datacenterId               = null;
    /** 毫秒内序列(0~4095) */
    private $sequence                   = null;
    /** 上次生成ID的时间截 */
    private $lastTimestamp              = null;

    /**
     * [SnowflakeIdWorker 构造函数]
     * @param [type] $workerId     [工作ID (0~31)]
     * @param [type] $datacenterId [数据中心ID (0~31)]
     */
    public function SnowflakeIdWorker($workerId, $datacenterId)
    {
        $this->twepoch                      = 1420041600000;
        /** 机器id所占的位数 */
        $this->workerIdBits                 = 5;
        $this->datacenterIdBits             = 5;
        $this->maxWorkerId                  = -1 ^ (-1 << $this->workerIdBits);
        $this->maxDatacenterId              = -1 ^ (-1 << $this->datacenterIdBits);
        $this->sequenceBits                 = 12;
        /** 机器ID向左移12位 */
        $this->workerIdShift                = $this->equenceBits;
        /** 数据标识id向左移17位(12+5) */
        $this->datacenterIdShift            = $this->equenceBits + $this->workerIdBits;
        /** 时间截向左移22位(5+5+12) */
        $this->timestampLeftShift           = 0;
        /** 生成序列的掩码，这里为4095 (0b111111111111 =0xfff=4095) */
        $this->sequenceMask                 = -1 ^ (-1 << $this->sequenceBits);
        /** 毫秒内序列(0~4095) */
        $this->sequence                     = 0;
        /** 上次生成ID的时间截 */
        $this->lastTimestamp                = -1;

        if ($workerId > $maxWorkerId || $workerId < 0) {
            throw new Exception(sprintf("worker Id can't be greater than %d or less than 0", $maxWorkerId));
        }

        if ($datacenterId > $maxDatacenterId || $datacenterId < 0) {
            throw new Exception(sprintf("datacenter Id can't be greater than %d or less than 0", $maxDatacenterId));
        }

        $this->workerId = $workerId;
        $this->datacenterId = $datacenterId;
    }

    /**
     * [nextId 获得下一个ID]
     */
    public function nextId()
    {
        $this->timestamp = $this->timeGen();
        //如果当前时间小于上一次ID生成的时间戳，说明系统时钟回退过这个时候应当抛出异常
        if ($this->timestamp < $this->lastTimestamp) {
            throw new Exception(
                sprintf("Clock moved backwards.  Refusing to generate id for %d milliseconds", $lastTimestamp - $timestamp)
            );
        }

        //如果是同一时间生成的，则进行毫秒内序列
        if ($this->lastTimestamp == $this->timestamp) {
            $this->equence = ($this->equence + 1) & @$this->equenceMask;
            //毫秒内序列溢出
            if ($this->equence == 0) {
                //阻塞到下一个毫秒,获得新的时间戳
                $this->timestamp = $this->tilNextMillis($this->lastTimestamp);
            }
        } else {
            //时间戳改变，毫秒内序列重置
            $this->equence = 0;
        }

        //上次生成ID的时间截
        $this->lastTimestamp = $this->timestamp;
        //移位并通过或运算拼到一起组成64位的ID
        return (($this->timestamp - $this->twepoch) << $this->timestampLeftShift) //
                | ($this->datacenterId << $this->datacenterIdShift) //
                | ($this->workerId << $this->workerIdShift) //
                | $this->sequence;
    }

    /**
     * [tilNextMillis 阻塞到下一个毫秒，直到获得新的时间戳]
     * @param  [type] $lastTimestamp [上次生成ID的时间截]
     */
    protected function tilNextMillis($lastTimestamp)
    {
        $timestamp = $this->timeGen();
        while ($timestamp <= $lastTimestamp) {
            $timestamp = $this->timeGen();
        }
        return $timestamp;
    }

    /**
     * [timeGen 返回以毫秒为单位的当前时间]
     */
    protected function timeGen()
    {
        return  intval(microtime(true) * 1000);
    }
}