<?php
//+-------------------------------------------------------------
//| 
//+-------------------------------------------------------------
//| Author Liu LianSen <liansen@d3zz.com> 
//+-------------------------------------------------------------
//| Date 2018-07-13
//+-------------------------------------------------------------
if(!defined('LIANSEN_UTILS')) {
    define('LIANSEN_UTILS', true);

    /**
     * <pre>
     * 载入本地配置(如果配置文件同目录下具有同名的.local.php后缀的本地文件)
     * e.g.
     * App/Conf/db.php （公共配置，可以认为是一个模板）
     *  ~~~~
     *      $conf = array(
     *              'DB_TYPE' => 'mysql',
     *              'DB_HOST' => '112.124.33.139',
     *              //...
     *              );
     *  ~~~~
     *
     * APP/Conf/db.local.php （某个环境本地所使用的配置，可能是你的开发环境，也可能是测试环境、生产环境）
     *  ~~~~
     *      return array(
     *              'DB_HOST' => '127.0.0.1',
     *              );
     *  ~~~~
     *
     * 那么项目运行时 'DB_HOST' 的值 为 db.local.php中的 '127.0.0.1'
     * @param array $conf
     * @return array
     */
    function loadLocalConf(array &$conf)
    {
        $trace = @debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1);
        if (!$trace || !isset($trace[0]['file'])) return $conf;
        $localFile = substr($trace[0]['file'], 0, -4) . '.local.php';
        if (is_file($localFile) && is_readable($localFile)) {
            $_conf = include $localFile;
            is_array($_conf) && $conf = array_merge($conf, $_conf);
        }
        return $conf;
    }

    /**
     * 保持格式打印数组
     * @param $var
     * @param bool $exit
     */
    function print_obj($var, $pre=true , $exit = true)
    {
        $out = print_r($var,true);
        echo $pre ? "<pre>{$out}</pre>" : $out;
    }

    /**
     * 返回指定长度的电话号码<br/>
     * 原理是<br/>
     * 1.去除$phone中的所有非数字字符<br/>
     * 2.截取剩余字符中的后$length个字符
     * @param string $phone 电话号码
     * @param int $length 电话号码长度(默认中国大陆11位)
     * @return string
     */
    function trim_phone($phone, $length = 11)
    {
        $phone = trim($phone);
        $phone = preg_replace('/[^0-9]/', '', $phone);
        return substr($phone, 0 - $length);
    }


    /**
     * 检查$phone经过trim_phone函数处理后是否是一个有效的大陆手机号码<br/>
     * 如果是，返回经过处理后的普通电话号码<br/>
     * 否则，返回false
     * @param $phone
     * @return string|bool
     */
    function phone($phone)
    {
        $phone = trim_phone($phone);
        if (!!preg_match('/^1[3-9]\d{9}$/', $phone)) {
            return $phone;
        }
        return false;
    }
}