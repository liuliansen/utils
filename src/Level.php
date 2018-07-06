<?php
//+-------------------------------------------------------------
//| 
//+-------------------------------------------------------------
//| Author Liu LianSen <liansen@d3zz.com> 
//+-------------------------------------------------------------
//| Date 2018-07-02
//+-------------------------------------------------------------
namespace utils;

class Level
{

    static protected $ds = '/';


    /**
     * @return string
     */
    public static function getDs()
    {
        return self::$ds;
    }

    /**
     * 设置分隔符号
     * @param string $ds
     */
    public static function setDs($ds)
    {
        self::$ds = $ds;
    }

    /**
     * 将路径信息转换成数组
     * @param $trace string
     * @param string $root
     * @return array
     */
    public static function convertToLevelArray($trace,$root = '')
    {
        if( $root !== '' &&
            $root !== static::$ds &&
            substr($root,strlen($root)-1) !== static::$ds)
        {
            $root .= static::$ds;
        }

        $trace  = trim($trace);
        if($root !== '' && strpos($trace,$root) === 0){
            $trace  = substr_replace($trace,'',0,strlen($root));
        }

        $trace  = trim($trace,static::$ds);
        $levels = explode(static::$ds, $trace);
        return $levels;
    }

    /**
     * 返回从root开始指定层级$level上的内容<br/>
     * $level从0开始，0为去除$root后，当前顶层元素
     * @param $trace
     * @param int $level
     * @param string $root
     * @return null
     */
    public static function getLevel($trace, $level = 0,$root = '')
    {
        $levels = static::convertToLevelArray($trace,$root);
        return isset($levels[$level])? trim($levels[$level]) :null;
    }


    /**
     * 获取指定层级$level的父级节点内容<br/>
     * $level取值范围：-count(层级数) <= $level <= 0  <br/>
     * 0为当前最后一级内容，-n表示往前倒推n级。<br/>
     * e.g. -1为父级，-2为爷爷级，-3为太爷爷级
     * @param $trace
     * @param int $level
     * @return null|string
     */
    public static function getFathersLevel($trace, $level = 0)
    {
        $levels = array_reverse(static::convertToLevelArray($trace));
        $level = abs($level);
        return isset($levels[$level])? trim($levels[$level]) :null;
    }







}

