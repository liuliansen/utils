<?php
//+-------------------------------------------------------------
//| 
//+-------------------------------------------------------------
//| Author Liu LianSen <liansen@d3zz.com> 
//+-------------------------------------------------------------
//| Date 2018-05-15
//+-------------------------------------------------------------
namespace utils;


/**
 * Class LogHelper
 * @package utils
 * @method void log($msg,    $format = '') 记录一般日志
 * @method void error($msg,  $format = '') 记录错误日志
 * @method void info($msg,   $format = '') 记录一般信息日志
 * @method void notice($msg, $format = '') 记录提示日志
 * @method void alert($msg,  $format = '') 记录报警日志
 * @method void debug($msg,  $format = '') 记录调试日志
 */
class Logger
{
    protected $config = [
        'file_size'   => 2097152,
        'path'        => '/var/log/',
        'level'       => ['log', 'error', 'info', 'sql', 'notice', 'alert', 'debug'],
        'driver'      => ['\\utils\\logdriver\\File']
    ];

    /**
     * @var array 日志类型
     */
    protected static $type = ['log', 'error', 'info', 'sql', 'notice', 'alert', 'debug'];


    /**
     * 尚未输出的日志信息
     * @var array
     */
    protected $logs = [];

    /**
     * @var array
     */
    protected $drivers = [];

    /**
     * LogHelper constructor.
     * @param array $config
     */
    public function __construct($config = [])
    {
        if (is_array($config)) {
            $this->config = array_merge($this->config, $config);
        }

        foreach ($this->config['driver'] as $driver){
            $this->drivers[] = new $driver;
        }
    }


    /**
     * 记录日志
     * @param $type
     * @param $log
     * @param $format
     * @return bool
     */
    protected function record($type,$log,$format = '')
    {
        if(!in_array($type,$this->config['level'])) {
            return true;
        }
        foreach ($this->drivers as $driver){
            $driver->save($this->config,$type,$log,$format);
        }
        return  true;
    }

    /**
     * @param $name
     * @param $arguments
     */
    public function __call($name, $arguments)
    {
        if(in_array($name,self::$type)) {
            array_unshift($arguments,$name);
        }
        call_user_func_array([$this,'record'],$arguments);
    }

}
