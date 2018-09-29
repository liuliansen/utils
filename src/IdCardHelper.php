<?php
//+-------------------------------------------------------------
//| 
//+-------------------------------------------------------------
//| Author Liu LianSen <liansen@d3zz.com> 
//+-------------------------------------------------------------
//| Date 2018-07-26
//+-------------------------------------------------------------
namespace utils;

class IdCardHelper
{
    /**
     * 身份证区域前缀
     * @var array
     */
    protected  static  $idCardPrefix = ['362429','110107','140108','130227','411024',
                                    '370113','320623','210603','211324', '230107',
                                    '230802','320501','330601','341125','352230',
                                    '360100','540127','533323'];

    /**
     * 姓
     * @var array
     */
    static protected $familyName = ['刘','李','张','王','赵','钱','孙','黄','金','蒋','魏','卢',
                                    '鲁','常','颜','闫','后','刑','杜','关','邱','冯','秦','吕',
                                    '吴','梁','高','习','贺','马','朱','牛','欧阳','黄埔','公孙'];

    /**
     * 男名字库
     * @var array
     */
    static protected $maleChars = ['亮','雨','伟','涛','敏','军','建','翔','森','飞','晋',
                                    '羽','斌','志','强','海','华','平','武','杰','轩','浩',
                                    '程','宏','文','刚','岳','云','鹏','隆','山','磊','胜',
                                    '明','俊','吉','虎','安','鸿','凯','康','嘉','国','小'];

    /**
     * 女名字库
     * @var array
     */
    static protected $femaleChars = ['雨','敏','菲','秀','丽','婧','涵','蕊','语','翠','花',
                                    '云','玉','彤','风','丹','兰','钰','珍','瑶','莲','柳',
                                    '雪','蓝','蕾','蝶','珊','洛','涵','琪','梦','雅','悦',
                                    '芯','语','安','萍','娇','婉','婷','静','怡','雯','梅'];

    /**
     * 生成姓名
     * @return string
     */
    static public function createName($idCardNum = '')
    {
        $name = static::$familyName[mt_rand(0, count(static::$familyName)-1)];
        if($idCardNum){
            $isFemale = substr($idCardNum,16,1) % 2 == 0;
        }else{
            $isFemale = mt_rand(1,2) % 2 == 0;
        }
        $chars = $isFemale ? static::$femaleChars : static::$maleChars;
        $len = mt_rand(2,3);
        for ($i = 0; $i<$len-1; $i++){
            $name .= $chars[mt_rand(0, count($chars)-1)];
        }
        return $name;
    }

    /**
     * @param $id17
     * @return mixed
     */
    static public function getIDCardVerifyNumber($id17)
    {
        // 加权因子
        $factor = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);
        // 校验码对应值
        $verify_number_list = array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2');
        $checksum = 0;
        for ($i = 0; $i < strlen($id17); $i++) {
            $checksum += substr($id17, $i, 1) * $factor[$i];
        }
        $mod = $checksum % 11;
        $verify_number = $verify_number_list[$mod];
        return $verify_number;
    }

    /**
     * 生成身份证号
     * @param int $ageBegin  年龄范围段起始
     * @param int $ageEnd    年龄范围段结束
     * @return string
     */
    static public function createIdCardNum($ageBegin = 18,$ageEnd = 60)
    {
        $prefix = static::$idCardPrefix;
        $num  = $prefix[mt_rand(0,count($prefix)-1)];
        $today = new Date();
        $today->modify('-'.mt_rand($ageBegin,$ageEnd).' years');
        $num .= $today->format('Ymd').mt_rand(111,999);
        $num .= static::getIDCardVerifyNumber($num);
        return $num;
    }

    /**
     * 校验身份证号
     * @param $val
     * @return bool
     */
    public static function checkIdCardNo($val)
    {
        if(!is_string($val)) return false;
        if(strlen($val) == 15){
            return !!preg_match('/^\d{6}\d{2}(0[1-9]|1[012])(0[1-9]|[12]\d|3[01])\d{3}$/', $val);
        }else{
            return static::preg18($val) && static::validateCheckCode($val);
        }
    }


    /**
     * @param $id
     * @return int
     */
    protected static function preg18($id)
    {
        return preg_match('/^\d{6}(19|20)\d{2}(0[1-9]|1[012])(0[1-9]|[12]\d|3[01])\d{3}(\d|X)$/', $id);
    }


    /**
     * @param $id
     * @return bool
     */
    protected static function validateCheckCode($id)
    {
        $pid = strtoupper($id);
        $iYear = substr($pid, 6, 4);
        $iMonth = substr($pid, 10, 2);
        $iDay = substr($pid, 12, 2);
        if (checkdate($iMonth, $iDay, $iYear)) {
            $id17 = substr($pid, 0, 17);  //身份证证号前17位
            if (static::getIDCardVerifyNumber($id17) != substr($pid, -1)) {
                return false;
            } else {
                return true;
            }
        }
    }


}