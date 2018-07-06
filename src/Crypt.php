<?php
//+-------------------------------------------------------------
//| 
//+-------------------------------------------------------------
//| Author Liu LianSen <liansen@d3zz.com> 
//+-------------------------------------------------------------
//| Date 2017-09-18
//+-------------------------------------------------------------

namespace utils;

class Crypt
{
    private $iv;
    private $key;
    protected $randChars = '0123456789';

    function __construct($key , $iv = '')
    {
        $this->key = $key;
        $this->iv  = $iv ?: $this->createIv();
    }


    /**
     * @return string
     */
    public function getIv()
    {
        return $this->iv;
    }


    /**
     * 生成iv
     * @param int $len
     * @return string
     */
    protected function createIv($len = 16)
    {
        $total = strlen($this->randChars);
        $iv = '';
        for ($i=0; $i<$len; $i++){
            $rand = mt_rand(0,$total-1);
            $iv .= substr($this->randChars,$rand,1);
        }
        return $iv;
    }


    /**
     * 加密
     * @param $data
     * @param bool $converJson
     * @return string
     */
    public function encrypt($data,$converJson = true)
    {
        if($converJson) {
            $data = json_encode($data,JSON_UNESCAPED_UNICODE);
        }
        $keyLen = openssl_cipher_iv_length('AES-256-CBC');
        $mod = strlen($data) % $keyLen;
        $data .= str_repeat(chr($keyLen - $mod), $keyLen - $mod);
        return base64_encode(openssl_encrypt($data,'AES-256-CBC',$this->key,OPENSSL_RAW_DATA|OPENSSL_ZERO_PADDING ,$this->iv));
    }

    /**
     * 解密
     * @param $data
     * @return string
     */
    public function decrypt($data)
    {
        $data = openssl_decrypt(base64_decode($data),'AES-256-CBC',$this->key,OPENSSL_RAW_DATA,$this->iv);
        return trim($data);
    }

    /**
     * 生成加密的url地址
     * @param $data
     * @param $ext
     * @return string
     */
    public function encryptUrl($data,$ext = '.html')
    {
        $en = $this->encrypt($data);
        $en = str_replace(['/','+'],['__DS__','__X__'],$en);
        return urlencode($en).$ext;
    }

    /**
     * 解密url地址
     * @param $data
     * @return string
     */
    public function decryptUrl($data)
    {
        $data = str_replace(['__DS__','__X__'],['/','+'],$data);
        return $this->decrypt($data);
    }
}
