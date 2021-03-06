<?php
//+-------------------------------------------------------------
//| 验证器基类
//+-------------------------------------------------------------
//| Author Liu LianSen <liansen@d3zz.com> 
//+-------------------------------------------------------------
//| Date 2018-09-29
//+-------------------------------------------------------------
namespace utils;


class Validator
{
    /**
     * 字段检查规则
     * e.g.
     * [
     *   'xxx' => 'required|email'
     * ]
     * @var array
     */
    protected $rules = null;

    /**
     * 验证时需要的正则
     * @var array
     */
    protected $regexVars = [];

    protected $requires = [];

    protected $scenes = [];

    /**
     * 字段发生错误时的信息
     * e.g.
     * [
     *   'xxx.required' => '请输入邮箱地址',
     *   'xxx.email'   => '请输入有效的邮箱地址',
     * ]
     * @var array
     */
    protected $messages = [];

    /**
     * 默认的错误信息
     * @var array
     */
    protected $_messages = [
        'require' => 'Someone must be require but not set',
        'string'  => 'Value is not an string.',
        'length'  => 'error length.',
        'num'     => 'Value is not an numeric.',
        'value'   => 'error numeric value ',
    ];

    protected $data = null;

    protected $errStrs = [];

    public function __construct($data = null)
    {
        $this->data = $data;
    }

    /**
     * 添加正则验证表达式
     * @param $regex
     */
    public function addRegex($regex)
    {
        $this->regexVars = array_merge($this->regexVars,$regex);
    }


    /**
     * 获取所有错误
     * @return array
     */
    public function getAllError()
    {
        return $this->errStrs;
    }


    /**
     * 获取最后的错误
     * @return string
     */
    public function getLastError()
    {
        return $this->errStrs[count($this->errStrs)-1];
    }


    /**
     * 设置错误信息
     * @param $name
     * @param string $method
     * @param null $message
     */
    protected function setError($name,$method = '',$message = null)
    {
        if($name && $method && isset($message[$name.'.'.$method])){
            $this->errStrs[] = $message[$name.'.'.$method];
        }elseif($method && isset($this->_messages[$method])){
            $this->errStrs[] = $this->_messages[$method];
        }else {
            $this->errStrs[] = 'Some error happened,but without message configure.';
        }
    }

    /**
     * 字符串
     * @param $name
     * @param $var
     * @return bool
     */
    protected function string($name,$var)
    {
        return is_string($var);
    }

    /**
     * 数字
     * @param $name
     * @param $val
     * @return bool
     */
    protected function num($name,$val){
        return is_numeric($val);
    }

    /**
     * 数值
     * @param $min
     * @param $max
     * @param $name
     * @param $val
     * @return bool
     */
    protected function value($min,$max,$name,$val)
    {
        if(!is_numeric($val)){
            return false;
        }
        return $val <= $max && $val >= $min;
    }

    /**
     * 长度
     * @param $min
     * @param $max
     * @param $name
     * @param $val
     * @return bool
     */
    protected function length($min,$max,$name,$val)
    {
        $len = is_string($val)? mb_strlen($val):count($val);
        return $len <= $max && $len >= $min;
    }

    /**
     * 邮箱地址
     * @param $name
     * @param $var
     * @return bool
     */
    protected function email($name,$var){
        return filter_var($var,FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * ip
     * @param $name
     * @param $val
     * @return bool
     */
    protected function ip($name,$val)
    {
        return filter_var($val,FILTER_VALIDATE_IP) !== false;
    }

    /**
     * url
     * @param $name
     * @param $val
     * @return bool
     */
    protected function url($name,$val)
    {
        return filter_var($val,FILTER_VALIDATE_URL) !== false;
    }

    /**
     * array
     * @param $name
     * @param $val
     * @return bool
     */
    protected function arr($name,$val)
    {
        return is_array($val);
    }

    /**
     * in
     * @param array $values
     * @param $name
     * @param $val
     * @return bool
     */
    protected function in($values =[],$name,$val){
        return in_array($val,$values);
    }

    /**
     * notIn
     * @param array $values
     * @param $name
     * @param $val
     * @return bool
     */
    protected function notIn($values =[],$name,$val){
        return !in_array($val,$values);
    }

    /**
     * 手机号码验证
     * @param $name
     * @param $val
     * @return bool
     */
    protected function mobile($name,$val)
    {
        return $this->regex('/1[3-9]\d{9}/',$name,$val);
    }

    /**
     * 身份证规则
     * @param $name
     * @param $val
     * @return bool
     */
    protected function idCardNo($name,$val)
    {
        return IdCardHelper::checkIdCardNo($val);
    }

    /**
     * json字符串
     * @param $name
     * @param $val
     * @return bool
     */
    protected function json($name,$val)
    {
        return !!json_decode($val);
    }

    /**
     * 日期格式
     * @param $regexStr
     * @param $name
     * @param $val
     * @return bool
     */
    protected function regex($regexStr,$name,$val)
    {
        return is_scalar($val) && 1 === preg_match($regexStr,$val);
    }

    /**
     * 整数
     * @param $name
     * @param $value
     * @return mixed
     */
    protected function int($name,$value)
    {
        return false !== filter_var($value,FILTER_VALIDATE_INT);
    }

    /**
     * 比较值
     * @param $type
     * @param $value
     * @param $val
     * @param $data
     * @return bool
     * @throws \Exception
     */
    protected function compare($type, $value, $val, $data)
    {
        if(mb_substr($value,0,2) == '$.'){
            $key = mb_substr($value,2);
            if(!isset($data[$key])){
                return false;
            }
            $value = $data[$key];
        }
        switch ($type){
            case 'eq':
                return $val == $value;
            case 'lt':
                return $val < $value;
            case 'lte':
                return $val <= $value;
            case 'gt':
                return $val > $value;
            case 'gte':
                return $val >= $value;
        }
        throw new \Exception("unsupport compare type {$type}");
    }

    /**
     * 等于
     * @param $value
     * @param $name
     * @param $val
     * @param $data
     * @return bool
     */
    protected function eq($value,$name,$val,$data)
    {
        return $this->compare('eq',$value,$val,$data);
    }


    /**
     * 等于
     * @param $value
     * @param $name
     * @param $val
     * @param $data
     * @return bool
     */
    protected function gt($value,$name,$val,$data)
    {
        return $this->compare('gt',$value,$val,$data);

    }

    /**
     * 等于
     * @param $value
     * @param $name
     * @param $val
     * @param $data
     * @return bool
     */
    protected function lt($value,$name,$val,$data)
    {
        return $this->compare('lt',$value,$val,$data);
    }


    /**
     * 大于等于
     * @param $value
     * @param $name
     * @param $val
     * @param $data
     * @return bool
     */
    protected function gte($value,$name,$val,$data)
    {
        return $this->compare('gte',$value,$val,$data);
    }

    /**
     * 小于等于
     * @param $value
     * @param $name
     * @param $val
     * @param $data
     * @return bool
     */
    protected function lte($value,$name,$val,$data)
    {
        return $this->compare('lte',$value,$val,$data);
    }

    /**
     * empty
     * @param $name
     * @param $val
     * @return bool
     */
    protected function emptyVal($name,$val)
    {
        return empty($val);
    }

    /**
     * 中文
     * @param $name
     * @param $val
     * @return bool
     */
    protected function chs($name,$val)
    {
        return $this->regex('/^[\x{4e00}-\x{9fa5}]+$/u',$name,$val);
    }

    /**
     * 中文
     * @param $value
     * @param $name
     * @param $val
     * @return bool
     */
    protected function chsOrIn($value,$name,$val)
    {
        if(in_array($val,$value)){
            return true;
        }
        return $this->regex('/^[\x{4e00}-\x{9fa5}]+$/u',$name,$val);
    }




    /**
     * 检查数据是否符合规则
     * @param $data
     * @param $rules
     * @param null $message
     * @param bool $breakFirstError 遇见第一个错误时即退出
     * @return bool|mixed
     */
    public function check($data = null,$rules = null,$message = null,$breakFirstError = true)
    {
        is_null($data)    && $data    = $this->data;
        is_null($rules)   && $rules   = $this->rules;
        is_null($message) && $message = $this->messages;
        if(is_null($rules) || is_null($data)) {
            $this->errStrs[] = 'Unset check rule or data';
            return false;
        }
        $checkRet = null;
        if(is_string($rules)){
            $rules = $this->formatRule($rules);
            if(is_array($data)){
                foreach ($data as $k => $item){
                    foreach ($rules as $rule) {
                        $ret = call_user_func_array([$this, $rule[0]], array_merge($rule[1], [$k, $item,$data]));
                        if($rule[0] == 'emptyVal'){
                            if($ret) break;
                        }else {
                            if (!$ret) {
                                if (is_string($message)) {
                                    $this->errStrs[] = $message;
                                } else {
                                    $this->setError($k, $rule[0], $message);
                                }
                                $checkRet = false;
                                if ($breakFirstError) break;
                            }
                        }
                    }
                    if($checkRet === false) break;
                }
            }else{
                foreach ($rules as $rule) {
                    $ret = call_user_func_array([$this, $rule[0]], array_merge($rule[1], ['', $data,[]]));
                    if($rule[0] == 'emptyVal'){
                        if($ret) break;
                    }else {
                        if (!$ret) {
                            $checkRet = false;
                            if (is_string($message)) {
                                $this->errStrs[] = $message;
                            } else {
                                $this->setError('', $rule[0], $message);
                            }
                            if ($breakFirstError) break;
                        }
                    }
                }
            }
        }
        else{
            $_rules = [];
            foreach ($rules as $k => $rule){
                $_rules[$k] = $this->formatRule($rule);
            }
            foreach ($_rules as $name => $ruleSet){
                foreach ($ruleSet as $rule){
                    if($rule[0] == 'require'){
                        if(!isset($data[$name])){
                            $this->setError($name,'require',$message);
                            $checkRet = false;
                            if($breakFirstError) break;
                        }
                    }
                    elseif($rule[0] == 'emptyVal'){
                        if(!isset($data[$name]) || $this->emptyVal($name,$data[$name])){
                            break;
                        }
                    }
                    else{
                        if(isset($data[$name])) {
                            $val = $data[$name];
                            $ret = call_user_func_array([$this, $rule[0]], array_merge($rule[1], [$name, $val,$data]));
                            if (!$ret) {                            ;
                                $this->setError($name, $rule[0],$message);
                                $checkRet = false;
                                if($breakFirstError) break;
                            }
                        }
                    }
                }
                if($checkRet === false) break;
            }
        }
        if(is_null($checkRet)) $checkRet = true;

        return $checkRet;
    }


    /**
     * 格式检测方法配置
     * @param $methodSet
     * @return array|null
     */
    protected function formatMethodRule($methodSet)
    {
        $rule = null;
        if(strpos($methodSet,':') === false){
            if($methodSet == 'empty') $methodSet = 'emptyVal';
            $rule =  [$methodSet,[]];
        }else{
            list($method,$argStr) = explode(':',$methodSet);
            if($method == 'regex'){
                $args = [$this->regexVars[$argStr]];
            }else {
                if($method == 'empty') $method = 'emptyVal';
                $argStr = trim($argStr);
                if (preg_match('/^\[(.*)\]$/', $argStr, $m)) {
                    $args = [explode(',', $m[1])];
                } else {
                    $args = explode(',', $argStr);
                }
            }
            $rule= [$method,$args];
        }
        return $rule;
    }

    /**
     * 将规则配置转换成数组
     * @param $strRule
     * @return array
     */
    protected function formatRule($strRule)
    {
        $set = [];
        $exp = explode('|',$strRule);
        if(count($exp) > 1){
            foreach ($exp as $item){
                $set[] = $this->formatMethodRule($item);
            }
        }else{
            $set[] = $this->formatMethodRule($exp[0]);
        }
        return $set;
    }

    /**
     * 情景验证
     * @param $scene
     * @param $data
     * @param bool $breakFirstError 遇见第一个错误时即退出
     * @return bool
     */
    public function scene($scene,$data = null,$breakFirstError = true)
    {
        if(!isset($this->scenes[$scene])){
            $this->errStrs[] = 'Validator class "'.get_class($this)."\" undefined scene \"{$scene}\"";
            return false;
        }
        is_null($data)  && $data = $this->data;
        $_rules = [];
        foreach ($this->scenes[$scene] as $k){
            if(isset($this->rules[$k])){
                $_rules[$k] = $this->rules[$k];
            }
        }
        return $this->check($data,$_rules,null,$breakFirstError);
    }
}