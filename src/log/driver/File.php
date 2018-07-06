<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

namespace utils\log\driver;


/**
 * 本地化调试输出到文件
 */
class File
{
    protected $config = [
        'time_format' => ' c ',
        'single'      => false,
        'file_size'   => 2097152,
        'path'        => '/var/log/',
        'apart_level' => [],
        'max_files'   => 0,
    ];

    protected $writed = [];

    // 实例化并传入参数
    public function __construct($config = [])
    {
        if (is_array($config)) {
            $this->config = array_merge($this->config, $config);
        }
    }

    /**
     * 日志写入接口
     * @access public
     * @param array $log 日志信息
     * @return bool
     */
    public function save(array $log = [])
    {
        if ($this->config['single']) {
            $destination = $this->config['path'] . 'single.log';
        } else {

            if ($this->config['max_files']) {
                $filename = date('Ymd') . '.log';
                $files    = glob($this->config['path'] . '*.log');

                if (count($files) > $this->config['max_files']) {
                    unlink($files[0]);
                }
            } else {
                $filename = date('Ym') . '/' . date('d')  . '.log';
            }

            $destination = $this->config['path'] . $filename;
        }

        $path = dirname($destination);
        !is_dir($path) && mkdir($path, 0755, true);

        $info = '';
        foreach ($log as $type => $val) {
            $level = '';
            foreach ($val as $msg) {
                if (!is_string($msg)) {
                    $msg = var_export($msg, true);
                }
                $level .= '[ ' . $type . ' ] ' . $msg . "\r\n";
            }
            if (in_array($type, $this->config['apart_level'])) {
                // 独立记录的日志级别
                if ($this->config['single']) {
                    $filename = $path . DIRECTORY_SEPARATOR . $type . '.log';
                } elseif ($this->config['max_files']) {
                    $filename = $path . DIRECTORY_SEPARATOR . date('Ymd') . '_' . $type . '.log';
                } else {
                    $filename = $path . DIRECTORY_SEPARATOR . date('d') . '_' . $type  . '.log';
                }
                $this->write($level, $filename, true);
            } else {
                $info .= $level;
            }
        }
        if ($info) {
            return $this->write($info, $destination);
        }
        return true;
    }

    protected function write($message, $destination, $apart = false)
    {
        //检测日志文件大小，超过配置大小则备份日志文件重新生成
        if (is_file($destination) && floor($this->config['file_size']) <= filesize($destination)) {
            try {
                rename($destination, dirname($destination) . DIRECTORY_SEPARATOR . time() . '-' . basename($destination));
            } catch (\Exception $e) {
            }
            $this->writed[$destination] = false;
        }

        if (empty($this->writed[$destination])) {
            $now     = date($this->config['time_format']);
            $ip      = $_SERVER['REMOTE_ADDR'];
            $method  = $_SERVER['REQUEST_METHOD'];
            $uri     = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
            $message = "---------------------------------------------------------------\r\n[{$now}] {$ip} {$method} {$uri}\r\n" . $message;

            $this->writed[$destination] = true;
        }

        return error_log($message, 3, $destination);
    }

}
