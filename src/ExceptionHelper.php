<?php
//+-------------------------------------------------------------
//| 
//+-------------------------------------------------------------
//| Author Liu LianSen <liansen@d3zz.com> 
//+-------------------------------------------------------------
//| Date 2018-05-10
//+-------------------------------------------------------------
namespace utils;

class ExceptionHelper
{

    /**
     * 抛出错误请求异常
     * @param string $msg 自定义错误信息
     * @throws \Exception
     */
    static public function error_request($msg = '')
    {
        $msg = $msg?: Errors::ERROR_REQUEST_MSG;
        throw new \Exception($msg,Errors::ERROR_REQUEST);
    }


    /**
     * 抛出认证失败异常
     * @param string $msg 自定义错误信息
     * @throws \Exception
     */
    static public function auth_failed($msg = '')
    {
        $msg = $msg?: Errors::AUTH_FAILED_MSG;
        throw new \Exception($msg,Errors::AUTH_FAILED);
    }

    /**
     * 抛出认证失败异常
     * @param string $msg 自定义错误信息
     * @throws \Exception
     */
    static public function device_auth_failed($msg = '')
    {
        $msg = $msg?: Errors::AUTH_FAILED_MSG;
        throw new \Exception($msg,Errors::DEVICE_AUTH_FAILED);
    }


    /**
     * 冲突错误
     * @param string $msg
     * @throws \Exception
     */
    static public function conflict_error($msg = '')
    {
        $msg = $msg?: Errors::ERROR_REQUEST_MSG;
        throw new \Exception($msg,Errors::CONFLICT_CODE);
    }


    /**
     * 服务器500错误
     * @param string $msg
     * @throws \Exception
     */
    static public function server_error($msg = '')
    {
        $msg = $msg?: Errors::SERVER_ERROR_MSG;
        throw new \Exception($msg,Errors::SERVER_ERROR);
    }

    /**
     * 资源不存在404
     * @param string $msg
     * @throws \Exception
     */
    static public function not_found_error($msg = '')
    {
        $msg = $msg?: '记录不存在';
        throw new \Exception($msg,Errors::NOT_FOUND);
    }



    
}