<?php
/**
 *
 * 人民币和美元的中英文大写翻译
 *
 */
class MoneyConvert
{

    const MONEY_TYPE_RMB = 'RMB';

    const MONEY_TYPE_DOLLAR = 'DOLLAR';

    /*
    *人民币和美元的中英文大写翻译
    *
    **/
    public static function numbertowords($currency='rmb',$amount=0,$type='')
    {

        try{

            switch($currency){
                case self::MONEY_TYPE_RMB:
                    $currency = self::MONEY_TYPE_RMB;
                    break;
                case self::MONEY_TYPE_DOLLAR:

                    $currency = self::MONEY_TYPE_DOLLAR;

                    if(!in_array($type,MoneyTypeDOLLAR::getSupportType())){
                        $type = 'cents';
                    }

                    break;
                default:
                    $currency = self::MONEY_TYPE_RMB;
                    break;
            }

            return call_user_func_array(array('MoneyType'.$currency,'exec'), array($amount,$type));
        }catch(Exception $e){
            return $e->getMessage();
        }

    }

}

/**
 * 转美元大写
 */
class MoneyTypeDOLLAR
{
    private static $isInt = false;

    private static $arr1 = array("", " thousand", " million", " billion");

    private static $arr2 = array("zero", "ten", "twenty", "thirty", "forty", "fifty", "sixty", "seventy", "eighty", "ninety");

    private static $arr3 = array("zero", "one", "two", "three", "four", "five", "six", "seven", "eight", "nine");

    private static $arr4 = array("ten", "eleven", "twelve", "thirteen", "fourteen", "fifteen", "sixteen", "seventeen", "eighteen", "nineteen");

    private static $amount = 0;

    private static $decimalType = 'cents';

    private static $supportType = array(
        'cents'=>'美分表达(数字转换到文字)',
        'point'=>'美点表达(拼出大写字母)',
        'fraction'=>'分数表达法(只接受数字)',
    );

    public static $typeMappingFunction = array(
        'cents'=>'decimalnumber2',
        'point'=>'decimalpoint',
        'fraction'=>'decimalfraction',
    );

    public static function getSupportType()
    {
        return array_keys(self::$supportType);
    }

    public static function exec($amount,$type = '')
    {
        self::$amount = $amount;

        self::$decimalType = $type;

        if(self::isIntAmount()){
            #金额是整数
            return self::execIntAmount();
        }else{
            #金额是浮点型
            return self::execFloatAmount();
        }
    }

    protected static function isIntAmount()
    {
        if(strpos(self::$amount,'.') === false){
            self::$isInt = true;
            return true;
        }

        return false;
    }

    protected static function execIntAmount($integer = '')
    {
        $origin = self::$amount;

        if($integer != ''){
            self::$amount = $integer;
        }

        $b = strlen(self::$amount);
        $f = $h = 0;
        $g = "";
        $e = ceil($b / 3);
        $k = $b - $e * 3;
        $g = "";

        for ($f = $k; $f < $b; $f += 3) {
            ++$h;
            $num3 = $f >= 0 ? substr(self::$amount,$f, $f + 3) : substr(self::$amount,0, $k + 3);
            $strEng = self::English($num3);
            if ($strEng != "") {
                if ($g != "") $g .= ",";
                $g .= self::English($num3) . self::$arr1[$e - $h];
            }
        }

        return "输入金额：".$origin."。转换成大写为: ". strtoupper($g);
    }

    private static function English($a,$debug = false)
    {
        $strRet = "";
        if (strlen($a) == 3 && substr($a,0, 3) != "000") {
            if (substr($a,0, 1) != "0") {
                $strRet .= self::$arr3[substr($a,0, 1)] . " hundred";
                if (substr($a,1, 2) != "00") $strRet .= " and ";
            }
            $a = substr($a,1);
        }

        if (strlen($a) == 2) {

            if (substr($a, 0, 1) == "0") {//todo
                $a = substr($a, 1);
            } elseif (substr($a, 0, 1) == "1") {
                $strRet .= self::$arr4[substr($a, 1, 2)];
            } else {
                $strRet .= self::$arr2[substr($a, 0, 1)];

                if (substr($a, 1, 1) != "0") {
                    $strRet .= "-";
                }
                $a = substr($a, 1);

                if (strlen($a) == 1 && substr($a, 0, 1) != "0") $strRet .= self::$arr3[substr($a, 0, 1)];

            }
        }

        return $strRet;
    }

    protected static function execFloatAmount()
    {
        if(self::isIntAmount())
        {
            return self::execIntAmount();
        }

        list($integer,$decimal) = explode('.',self::$amount);

        $interword = self::execIntAmount($integer);

        if(strlen($decimal)>2){
            return "The figure is account to two decimal places.eg：8765.55";
        }else{
            $decimalword = call_user_func_array(array(__CLASS__,self::$typeMappingFunction[self::$decimalType]),array($decimal));

            $expression = "【".self::$supportType[self::$decimalType]."】";
        }

        return $interword." AND ".$decimalword.$expression;
    }

    private static function decimalnumber2($a)
    {
        $b = strlen($a);
        $f = $h = 0;
        $g = "";
        $e = ceil($b / 3);
        $k = $b - $e * 3;
        $g = "";

        for ($f = $k; $f < $b; $f += 3) {
            ++$h;
            $num3 = $f >= 0 ? substr($a,$f, $f + 3) : substr($a,0, $k + 3);
            $strEng = self::English($num3);
            if ($strEng != "") {
                if ($g != "") $g .= ",";
                $g .= self::English($num3) . self::$arr1[$e - $h];
            }
        }
        return "CENTS ".strtoupper($g)." ONLY";
    }

    private static function decimalpoint($a)
    {
        $b = strlen($a);
        $f = $h = 0;
        $g = "";
        $e = ceil($b / 3);
        $k = $b - $e * 3;
        $g = "";
        for ($f = $k; $f < $b; $f += 3) {
            ++$h;
            $num3 = $f >= 0 ? substr($a,$f, $f + 3) : substr($a,0, $k + 3);
            $strEng = self::English($num3);
            if ($strEng != "") {
                if ($g != "") $g .= ",";
                $g .= self::English($num3) . self::$arr1[$e - $h];
            }
        }
        return "POINT ".strtoupper($g)." ONLY";
    }

    private static function decimalfraction($a)
    {
        $b = strlen($a);
        $f = $h = 0;
        $g = "";
        $e = ceil($b / 3);
        $k = $b - $e * 3;
        $g = "";
        for ($f = $k; $f < $b; $f += 3) {
            ++$h;
            $num3 = $f >= 0 ? substr($a,$f, $f + 3) : substr($a,0, $k + 3);
            $strEng = self::English($num3);
            if ($strEng != "") {
                if ($g != "") $g .= ",";
                $g .= self::English($num3) . self::$arr1[$e - $h];
            }
        }
        return strtoupper($g);
    }

}

/**
 * 转人民币大写
 */
class MoneyTypeRMB
{

    public static function exec($amount,$type = '')
    {
        $b = 9.999999999999E10;
        $f = "零";
        $h = "壹";
        $g = "贰";
        $e = "叁";
        $k = "肆";
        $p = "伍";
        $q = "陆";
        $r = "柒";
        $s = "捌";
        $t = "玖";
        $l = "拾";
        $d = "佰";
        $i = "仟";
        $m = "万";
        $j = "亿";
        $o = "元";
        $c = "角";
        $n = "分";
        $v = "整";

        $amount = strval($amount);

        $origin = $amount;

//        var_dump($amount);exit;
        if ($amount == "") {
            throw new Exception("Use only numbers!");
        }

        if (preg_match_all('/^[\,\.]/i',$amount,$abc)) {
//            print_r($abc);
            throw new Exception("Use only numbers!!");
        }

        if (!preg_match('/^((\d{1,3}(,\d{3})*(.((\d{3},)*\d{1,3}))?)|(\d+(.\d+)?))$/',$amount)) {
            throw new Exception("Use only numbers!!!");
        }

        $amount = str_replace(",", "",$amount);
        $amount = ltrim($amount,'0');

        if (intval($amount) > $b) {
            throw new Exception("Maximum number is 99999999999.99!");
        }

        $b = explode(".",$amount);
        if (count($b) > 1) {
            $amount = $b[0];
            $b = $b[1];
            $b = substr($b,0, 2);
        } else {
            $amount = $b[0];
            $b = "";
        }

        $h = [$f, $h, $g, $e, $k, $p, $q, $r, $s, $t];
        $l = ["", $l, $d, $i];
        $m = ["", $m, $j];
        $n = [$c, $n];
        $c = "";

        if(intval($amount)>0){


            for ($j = 0,$d =0; $d<strlen($amount);$d++){
                $e = strlen($amount) - $d - 1;
                $i = substr($amount,$d,1);
                $g = $e/4;
                $e = $e%4;

                if($i == "0"){
                    $j++;
                }else{
                    if($j > 0){
                        $c .= $h[0];
                    }
                    $j = 0;
                    $c .= $h[intval($i)] . $l[$e];
                }

                if(($e == 0) && ($j < 4)){
                    $c .= $m[$g];
                }
            }
            $c .= $o;
        }

        if($b != ""){
            for($d = 0;$d < strlen($b);$d++){
                $i = substr($b,$d,1);
                if($i != "0"){
                    $c .= $h[intval($i)] . $n[$d];
                }
            }
        }

        if ($c == "") $c = $f . $o;
        if (strlen($b) < 2) $c .= $v;

        return "输入金额：".$origin."。转换成大写为: ". $c;
    }


}

$money = '100050.23';

echo MoneyConvert::numbertowords(MoneyConvert::MONEY_TYPE_RMB,$money);

echo "<br>";

echo MoneyConvert::numbertowords(MoneyConvert::MONEY_TYPE_DOLLAR,$money,'cents');

echo "<br>";

echo MoneyConvert::numbertowords(MoneyConvert::MONEY_TYPE_DOLLAR,$money,'point');

echo "<br>";

echo MoneyConvert::numbertowords(MoneyConvert::MONEY_TYPE_DOLLAR,$money,'fraction');
