<?php
//+-------------------------------------------------------------
//| 
//+-------------------------------------------------------------
//| Author Liu LianSen <liansen@d3zz.com> 
//+-------------------------------------------------------------
//| Date 2017-10-28
//+-------------------------------------------------------------
namespace utils;


class CodeGen
{
    /**
     * 纯数字
     * @var int
     */
    const TYPE_ONLY_NUM  = 1;

    /**
     * 纯小写字符串
     * @var int
     */
    const TYPE_ONLY_LOWER_CHAR = 2;

    /**
     * 纯大写字符串
     * @var int
     */
    const TYPE_ONLY_UPPER_CHAR = 3;

    /**
     * 数字大小写字母混合
     * @var int
     */
    const TYPE_MIX       = 4;

    /**
     * 数字小写字母混合
     * @var int
     */
    const TYPE_MIX_LOWER  = 5;

    /**
     * 数字大写字母混合
     * @var int
     */
    const TYPE_MIX_UPPER  = 6;

    /**
     * 数字、大小写字母、特殊符号混合
     * @var int
     */
    const TYPE_COMPLEX        = 7;

    /**
     * 数字、小写字母、特殊符号混合
     * @var int
     */
    const TYPE_COMPLEX_LOWER  = 8;

    /**
     * 数字、大写字母、特殊符号混合
     * @var int
     */
    const TYPE_COMPLEX_UPPER  = 9;

    static protected $num        = '0123456789';
    static protected $lowerChars = 'abcdefghijklmnopqrstuvwxyz';
    static protected $uperChars  = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    static protected $symbols    = '!@#$%^&*-+=_';


    /**
     * 生成指定长度和格式的字符串编码
     * @param int $len
     * @param int $type
     * @return string
     * @throws \Exception
     */
    static public function gen($len = 16,$type = CodeGen::TYPE_MIX_UPPER)
    {
        switch ($type){
            case CodeGen::TYPE_ONLY_NUM:
                $seed = static::$num;
                break;
            case CodeGen::TYPE_ONLY_LOWER_CHAR:
                $seed = static::$lowerChars;
                break;
            case CodeGen::TYPE_ONLY_UPPER_CHAR:
                $seed = static::$uperChars;
                break;
            case CodeGen::TYPE_MIX:
                $seed = static::$num.static::$lowerChars.static::$uperChars;
                break;
            case CodeGen::TYPE_MIX_LOWER:
                $seed = static::$num.static::$lowerChars;
                break;
            case CodeGen::TYPE_MIX_UPPER:
                $seed = static::$num.static::$uperChars;
                break;
            case CodeGen::TYPE_COMPLEX:
                $seed = static::$num.static::$lowerChars.static::$uperChars.static::$symbols;
                break;
            case CodeGen::TYPE_COMPLEX_LOWER:
                $seed = static::$num.static::$lowerChars.static::$symbols;
                break;
            case CodeGen::TYPE_COMPLEX_UPPER:
                $seed = static::$num.static::$uperChars.static::$symbols;
                break;
            default: throw new \Exception('不支持的code生成类型');
        }
        return static::_gen($len,$seed);
    }


    /**
     * @param $len
     * @param $seed
     * @return string
     */
    static protected function _gen($len,$seed)
    {
        $code = '';
        for ($i=0; $i<$len; $i++)
        {
            $index = mt_rand(0,strlen($seed)-1);
            $code .= $seed[$index];
        }
        return $code;
    }


}