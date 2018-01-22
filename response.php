<?php
/**
 * APP接口
 * User: YT
 * Date: 2018/1/20 0020
 * Time: 16:41
 */

class Response{

    const JSON = 'json';

    /**
     * 按综合方式返回数据
     * @param $code
     * @param $message
     * @param array $data
     * @param $type
     */
    public static function show($code, $message, $data=[], $type=self::JSON){
        if(!is_numeric($code)){
            return '';
        }

        $type = isset($_GET['format'])?$_GET['format']:$type;

        $result = array(
            'code' => $code,
            'message' => $message,
            'data' => $data
        );

        switch ($type){
            case 'json':
                self::json($code, $message, $data);
                break;
            case 'xml':
                self::xml($code, $message, $data);
                break;
            default:
                var_dump($result);
        }
    }

    /**
     * 返回json格式的数据
     * @param $code
     * @param $message
     * @param array $data
     * @return string
     */
    public static function json($code, $message, $data=[]){
        if(!is_numeric($code)){
            return '';
        }
        $result = array(
            'code' => $code,
            'message' => $message,
            'data' => $data
        );
        echo json_encode($result);
        exit;
    }

    /**
     * 返回xml数据
     * @param $code
     * @param $message
     * @param array $data
     * @return string
     */
    public static function xml($code, $message, $data=[]){

        if(!is_numeric($code)){
            return '';
        }
        header('Content-type:text/xml;charset="utf-8"');

        $xml = "<?xml version='1.0' encoding='utf-8'?>\n";
        $xml .= "<root>\n";
        $xml .= "<code>".$code."</code>\n";
        $xml .= "<message>".$message."</message>\n";
        $xml .= "<data>\n";
        $xml .= SELF::xmlEncode($data);
        $xml .= "</data>\n";
        $xml .= "</root>\n";

        echo $xml;
        exit;
    }

    public static function xmlEncode($data){
        $xml=$attr="";
        foreach ($data as $key => $v){
            if(is_numeric($key)){
                $attr = "id='{$key}'";
                $key = "item";
            }
            $xml .= "<{$key} {$attr}>";
            $xml .= is_array($v)?SELF::xmlEncode($v):$v;
            $xml .= "</{$key}>\n";
        }
        return $xml;
    }
}