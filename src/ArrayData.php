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
        return array_values(array_filter($routes));
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



}