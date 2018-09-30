<?php
//+-------------------------------------------------------------
//| 
//+-------------------------------------------------------------
//| Author Liu LianSen <liansen@d3zz.com> 
//+-------------------------------------------------------------
//| Date 2018-09-30
//+-------------------------------------------------------------
namespace utils\logdriver;

class File
{
    /**
     * 自定义格式存储
     * @param $config
     * @param $type
     * @param $log
     * @param string $format
     * @return bool
     */
    public function save($config,$type,$log,$format = '')
    {
        $destination =$config['path'].DIRECTORY_SEPARATOR.date('Ym').DIRECTORY_SEPARATOR.date('d').'.log';
        $path = dirname($destination);
        !is_dir($path) && mkdir($path, 0755, true);
        //检测日志文件大小，超过配置大小则备份日志文件重新生成
        if (is_file($destination) && floor($config['file_size']) <= filesize($destination)) {
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

}