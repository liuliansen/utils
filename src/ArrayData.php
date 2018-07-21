<?php
//+-------------------------------------------------------------
//| 
//+-------------------------------------------------------------
//| Author Liu LianSen <liansen@d3zz.com> 
//+-------------------------------------------------------------
//| Date 2018-07-21
//+-------------------------------------------------------------
namespace utils;

class ArrayData
{

    protected $arr = [];

    public function __construct(array $arr)
    {
        $this->arr = $arr;
    }


    /**
     * 转化数据类型
     * @param mixed $value
     * @param string $type [string|int|float|bool|json]
     * @return bool|float|int|string|array
     */
    protected function convert($value,$type = 'string',$default)
    {
        switch (strtolower($type)){
            case 'string':
                return strval($value);
            case 'int' :
                return intval($value);
            case 'float':
                return floatval($value);
            case 'bool':
                return boolval($value);
            case 'json':
                $ret = json_decode($value,true);
                if(is_null($ret)) $ret = $default;
                return $ret;
        }
        return $value;
    }

    /**
     * 获取数组中指定路径的值
     * @param $arr
     * @param $keys
     * @return mixed|null
     */
    protected function getPositionValue($arr, $keys)
    {
        if(empty($keys)){
            return $arr;
        }
        if(count($keys) === 1){
            return isset($arr[$keys[0]]) ? $arr[$keys[0]] : null;
        }
        return isset($arr[$keys[0]]) ?
            $this->getPositionValue($arr[$keys[0]],array_slice($keys,1))
            :null;
    }

    /**
     * 获取数组值
     * @param $key
     * @param string $type
     * @param string $default
     * @return array|bool|float|int|string
     */
    public function get($key, $type = 'string', $default = '')
    {
        if(strpos($key,'.') === false){
            if (isset($this->arr[$key])){
                return $this->convert($this->arr[$key],$type,$default);
            }else return $default;
        }else{
            $val = $this->getPositionValue($this->arr,explode('.',$key));
            if(is_null($val)){
                return $default;
            }else  return $this->convert($val,$type,$default);
        }
    }


}