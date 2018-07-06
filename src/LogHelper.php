<?php
//+-------------------------------------------------------------
//| 
//+-------------------------------------------------------------
//| Author Liu LianSen <liansen@d3zz.com> 
//+-------------------------------------------------------------
//| Date 2018-05-15
//+-------------------------------------------------------------
namespace utils;

use utils\log\driver\File;


/**
 * Class LogHelper
 * @package utils
 * @method void log($msg,    $dir = '', $format = '') static 记录一般日志
 * @method void error($msg,  $dir = '') static 记录错误日志
 * @method void info($msg,   $dir = '') static 记录一般信息日志
 * @method void notice($msg, $dir = '') static 记录提示日志
 * @method void alert($msg,  $dir = '') static 记录报警日志
 * @method void debug($msg,  $dir = '') static 记录调试日志
 */
class LogHelper
{

    static protected $fileSize = 2097152;

    /**
     * @var array 日志类型
     */
    static  protected  $type = ['log', 'error', 'info', 'notice', 'alert', 'debug'];

    /**
     * 写入日志
     * @throws \Exception
     */
    static public function record()
    {
        $argv = func_get_args();
        $argc = count($argv);
        if($argc == 2){
            Log::record($argv[0],$argv[1]);
            Log::save();
            return;
        }elseif($argc == 3){
            $writer = new File(['path' => $argv[1]]);
            $log = [$argv[2] => [$argv[0]]];
            $writer->save($log);
            return;
        }elseif($argc == 4){
            static::save($argv);
            return;
        }
        throw new \Exception('不支持的日志参数');
    }


    static protected function save($args)
    {
        $destination = $args[1].date('Ym').DIRECTORY_SEPARATOR.date('d').'.log';
        $path = dirname($destination);
        !is_dir($path) && mkdir($path, 0755, true);
        //检测日志文件大小，超过配置大小则备份日志文件重新生成
        if (is_file($destination) && floor(static::$fileSize) <= filesize($destination)) {
            try {
                rename($destination, dirname($destination) . DIRECTORY_SEPARATOR . time() . '-' . basename($destination));
            } catch (\Exception $e) {}
        }

        $msg = static::format($args[0],$args[2]);
        return error_log($msg, 3, $destination);
    }

    /**
     * 格式化日志内容
     * @param $content
     * @param $format
     * @return mixed
     */
    static protected function format($content,$format)
    {
        preg_match_all('/%t\(([^%]+)\)/',$format,$m);
        if(isset($m[1])){
            for ($i = 0; $i<count($m[1]); $i++){
                $format = str_replace($m[0][$i],date($m[1][$i]),$format);
            }
        }
        $msg = str_replace('%C',$content,$format);
        return $msg.PHP_EOL;
    }



    /**
     * @param $name
     * @param $arguments
     */
    static public function __callStatic($name, $arguments)
    {
        if(in_array($name,self::$type)) {
            array_push($arguments,$name);
        }
        call_user_func_array(self::class.'::record',$arguments);
    }

}
