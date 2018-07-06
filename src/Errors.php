<?php
//+-------------------------------------------------------------
//| 
//+-------------------------------------------------------------
//| Author Liu LianSen <liansen@d3zz.com> 
//+-------------------------------------------------------------
//| Date 2018-05-10
//+-------------------------------------------------------------
namespace utils;


class Errors
{
    const ERROR_REQUEST      = 400;
    const ERROR_REQUEST_MSG  = '错误的请求';

    const AUTH_FAILED        = 401;
    const DEVICE_AUTH_FAILED = 402;
    const AUTH_FAILED_MSG    = '用户认证失败';
    const LOGIN_TIME_OUT     = 403; //登录超时
    const NOT_FOUND          = 404;


    const REPEAT_REQUEST     = 406;
    const REPEAT_REQUEST_MSG = '请求过于频繁';
    const USER_REGISTER_REPEAT_MSG = '电话号码已被注册';

    const CONFLICT_CODE      = 409;

    const SERVER_ERROR       = 500;
    const SERVER_ERROR_MSG   = '服务器繁忙，请稍后再试';

    const REMOTE_ERROR       = 503;

    
}