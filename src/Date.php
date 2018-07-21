<?php
//+-------------------------------------------------------------
//| 日期工具类
//| 除本类中的方法之外，可以直接调用DateTime的方法
//+-------------------------------------------------------------
//| Author Liu LianSen <liansen@d3zz.com> 
//+-------------------------------------------------------------
//| Date 2017-09-23
//+-------------------------------------------------------------
namespace utils;


class Date
{
    /**
     * @var \DateTime
     */
    protected $date;

    protected $defaultFormat = 'Y-m-d H:i:s';


    /**
     * Date constructor.
     * <pre>
     * 支持的参数格式：
     * new Date(); //使用当前时间初始
     * new Date($date); //$date为一个有效时间戳，或格式为'Y-m-d H:i:s'的日期字符串
     * new Date($format, $date); //$date为一个有效时间戳，或格式为$format的日期字符串
     * </pre>
     * @throws \Exception
     */
    public function __construct()
    {
        $args = func_get_args();
        switch (count($args)){
            case 0:
                $this->date = date_create();
                break;
            case 1:
                $date = $args[0];
                if(is_int($args[0])){
                    $date = date($this->defaultFormat,$args[0]);
                }
                $this->date = date_create_from_format($this->defaultFormat, $date);
                break;
            case 2:
                $date = $args[1];
                if(is_int($args[1])){
                    $date = date($args[0], $args[1]);
                }
                $this->date = date_create_from_format($args[0], $date);
                break;
            default:
                throw new \Exception('不支持的构造参数');
        }
        if($this->date === false){
            throw new \Exception('日期初始失败，请检查参数是否符合格式');
        }
    }


    /**
     * 格式化输出当前时间
     * @param  string $format
     * @return string
     */
    public function format($format = '')
    {
        $format = $format ?: $this->defaultFormat;
        return $this->date->format($format);
    }

    /**
     * 获取时间戳
     * @return int
     */
    public function unixTime()
    {
        return $this->date->getTimestamp();
    }


    /**
     * 获取年份
     * @return string
     */
    public function year()
    {
        return $this->date->format('Y');
    }

    /**
     * 获取月份
     * @return string
     */
    public function month()
    {
        return $this->date->format('m');
    }

    /**
     * 获取月份
     * @return string
     */
    public function day()
    {
        return $this->date->format('d');
    }


    /**
     * @see \DateTime::modify()
     * @param $modify
     * @return static
     */
    public function modify($modify)
    {
        $this->date->modify($modify);
        return $this;
    }

    /**
     * 返回当前日期和传入日期对象的天数差
     * @param Date $date
     * @return int
     */
    public function diff(Date $date)
    {
        $diff = $this->date->diff($date->getDateObject());
        return intval($diff->format('%r%a')) * -1;
    }


    /**
     * @return \DateTime|false
     */
    public function getDateObject()
    {
        return $this->date;
    }


    /**
     * @param $name
     * @param $arguments
     * @return mixed
     * @throws \Exception
     */
    public function __call($name, $arguments)
    {
        if(method_exists($this->date,$name)){
            return call_user_func_array([$this->date,$name],$arguments);
        }
        throw new \Exception('Unknown method "'.$name.'"');
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->format();
    }


    /**
     * 获取下一个月同日
     * @return Date
     */
    public function getNextMonthDate()
    {
        $date = new Date($this->format());
        return $date->modify('+1 months');
    }


    /**
     * 获取指定日期前n个月日期
     * @param int $months
     * @param bool $includeCurrentMonth 是否包含当前对象的月份
     * @return Date
     */
    public function getNMonthsAgo($months, $includeCurrentMonth = false)
    {
        $_d = clone $this;
        if($includeCurrentMonth) $months -=1;
        $_d->modify('-'.$months. 'months');
        return $_d;
    }

    /**
     * 魔术clone方法
     * 返回一个独立的date对象
     */
    public function __clone()
    {
        $this->date = clone $this->date;
    }

    /**
     * 检查当前年份是不是闰年
     * @param string|int $year yyyyy
     * @return bool
     */
    public static function isLeapYear($year)
    {
        if($year % 100 == 0){
            return $year % 400 == 0;
        }
        return $year % 4 == 0;
    }

    /**
     * 获取指定月份天数
     * @param $moth
     * @param string $year
     * @return int
     */
    public static function getDaysOfMonth($moth,$year = '')
    {
        if(in_array($moth,[1,3,5,7,8,10,12])){
            return 31;
        }
        if($moth == 2){
            return Date::isLeapYear($year) ? 29 : 28;
        }
        return 30;
    }


}