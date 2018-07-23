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
     * @param $default
     * @return array|bool|float|int|string
     */
    protected function convert($value,$type = 'string',$default)
    {
        switch (strtolower($type)){
            case 'string':
                return strval($value) ?: $default;
            case 'int' :
                return intval($value)  ?: $default;
            case 'float':
                return floatval($value)  ?: $default;
            case 'bool':
                return boolval($value)  ?: $default;
            case 'json':
                $ret = json_decode($value,true);
                if(is_null($ret)) $ret = $default;
                return $ret;
            default: return $value ?: $default;
        }
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
     * 获取数组指定位置值
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
        }

        $val = $this->getPositionValue($this->arr,explode('.',$key));
        if(is_null($val)){
            return $default;
        }else  return $this->convert($val,$type,$default);
    }


    /**
     * 解析路由
     * @param $route
     * @return array
     */
    protected function getRoute($route)
    {
        $routes = preg_split('/[\{\}\[\]\.]/',$route);
        return array_values(array_filter($routes,function($v){
            return $v !== ''&& $v !== false;
        }));
    }


    /**
     * <pre>
     * 在数组中使用路由表达式查找指定的值
     * e.g.     *
     * $arr = [
     *      'a' => 123,
     *      'b' => [
     *          'c' => 345,
     *          'd' => [
     *              'e' => 789,
     *          ],
     *      ],
     *      'data' => [
     *          'users' => [
     *              [
     *                  'name' => 'jerry',
     *                  'cards' => [
     *                      [
     *                          'id' => 2,
     *                          'card_no' => 123456
     *                      ]
     *                  ]
     *              ],
     *              [
     *                  'name' => 'tom',
     *                  'cards' => [
     *                      [
     *                          'id' => 1,
     *                          'card_no' => 987654
     *                      ]
     *                  ]
     *              ]
     *      ]
     * ]
     * ];
     * $ad = new \utils\ArrayData($arr);
     * $ad->query('a');                                                  // string(3) "123"
     * $ad->query('b.d.e','int',0) );                                    // int(789)
     * 支持的路由分割符为: '{'、'}'、'['、']'、'.'
     * 比如
     *   data.users.name=tom.cards.id=1.card_no
     *   data.users[name=tom].cards[id=1].card_no
     *   {data.users[name=tom]}.{cards[id=1]}.card_no
     * 三条路由获取的结果是一样的。
     * 选择哪种分隔符取决于个人喜好，个人推荐第二种写法，具有良好的可读性
     *      通过 '.' 表明前者是一个对象(关联数组)
     *      通过 'xxx[key=val]'，表明选取xxx字段下具有特定属性值的元素
     *
     * $ad->query('data.users[name=tom{cards[id=1.card_no]}]');          // string(6) "987654"
     * $ad->query('data.users[name=tom{cards[id=3.card_no]}]','int',0 ); // int(0)
     * </pre>
     * @param string $route     路由
     * @param string $type      数据类型
     * @param string $default   不存在时的默认值
     * @param null $data
     * @return array|bool|float|int|string
     */
    public function query($route,$type = 'string',$default = '',$data = null)
    {
        $routes  = $this->getRoute($route);
        $routCnt = count($routes);
        $data    = $data ?: $this->arr;
        $ret     = null;
        for($i=0;$i<$routCnt; $i++){
            $r = $routes[$i];
            if(strpos($r,'=') === false){
                if(isset($data[$r])) {
                    if ($i == $routCnt - 1) {
                       $ret = $data[$r];
                    }
                    $data = $data[$r];
                }else break;
            }else{
                list($k,$v) = explode('=',$r);
                $found = false;
                foreach ($data as $item){
                    if(isset($item[$k]) && $item[$k] == $v){
                        $found = true;
                        if ($i == $routCnt - 1) {
                            $ret = $item;
                        }
                        $data = $item;
                    }else continue;
                }
                if(!$found) break;
            }
        }
        return $this->convert($ret,$type,$default);
    }


    /**
     * <pre>
     *  获取指定范围段内元素字段数组
     *  内部实现原理基于array_slice方法先取出所需范围元素
     *  然后对元素进行所需字段提取或填充
     * </pre>
     * @param int $offset 如果 offset 非负，则序列将从 array 中的此偏移量开始。如果 offset 为负，则序列将从 array 中距离末端这么远的地方开始。
     * @param int $length 如果给出了 length 并且为正，则序列中将具有这么多的单元。如果给出了 length 并且为负，则序列将终止在距离数组末端这么远的地方。如果省略，则序列将从 offset 开始一直到 array 的末端。
     * @param array $keys 需要提取的键位关联数组;['name' => ''] //键名 => 默认值
     * @param array $arr  默认使用当前对象$arr内容进行处理
     * @return array|bool
     */
    public function slice($offset ,$length = null,$keys = [],array $arr = [])
    {
        if(!is_array($arr)){
            return false;
        }
        if(empty($arr)){
            $arr = $this->arr;
        }
        $_arr = array_slice($arr,$offset,$length);
        $ret = [];
        for ($i=0; $i<count($_arr); $i++){
            $el = $_arr[$i];
            if(empty($keys)){
                $ret[] = $el;
                continue;
            }
            $loc = [];
            foreach ($keys as $k => $def){
                if(isset($el[$k])){
                    $loc[$k] = $el[$k];
                }else{
                    $loc[$k] = $def;
                }
            }
            $ret[] = $loc;
        }
        return $ret;





    }



}