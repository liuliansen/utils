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
class LogHelper
{
    protected $config = [
        'file_size'   => 2097152,
        'path'        => '/var/log/',
        'level'       => ['log', 'error', 'info', 'sql', 'notice', 'alert', 'debug'],
    ];


    /**
     * 尚未输出的日志信息
     * @var array
     */
    protected $logs = [];

    /**
     * LogHelper constructor.
     * @param array $config
     */
    public function __construct($config = [])
    {
        if (is_array($config)) {
            $this->config = array_merge($this->config, $config);
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
        return  $this->save($type,$log,$format);
    }


    /**
     * 自定义格式存储
     * @param $type
     * @param $log
     * @param string $format
     * @return bool
     */
    protected function save($type,$log,$format = '')
    {
        $destination = $this->config['path'].DIRECTORY_SEPARATOR.date('Ym').DIRECTORY_SEPARATOR.date('d').'.log';
        $path = dirname($destination);
        !is_dir($path) && mkdir($path, 0755, true);
        //检测日志文件大小，超过配置大小则备份日志文件重新生成
        if (is_file($destination) && floor($this->config['file_size']) <= filesize($destination)) {
            try {
                rename($destination, dirname($destination) . DIRECTORY_SEPARATOR . time() . '-' . basename($destination));
            } catch (\Exception $e) {}
        }
        if($format){
            $msg = static::format($type,$log,$format);
        }else{
            $msg = date('Y-m-d H:i:s')."\t[{$type}]\t{$log}".PHP_EOL;
        }
        return error_log($msg, 3, $destination);
    }

    /**
     * 格式化日志内容
     * @param  $type
     * @param  $content
     * @param  $format
     * @return mixed
     */
    static protected function format($type,$content,$format)
    {
        preg_match_all('/%t\(([^%]+)\)/',$format,$m);
        if(isset($m[1])){
            for ($i = 0; $i<count($m[1]); $i++){
                $format = str_replace($m[0][$i],date($m[1][$i]),$format);
            }
        }
        $msg = str_replace(['%T','%C'],[$type,$content],$format);
        return $msg.PHP_EOL;
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
        call_user_func_array(self::class.'::record',$arguments);
    }

}
