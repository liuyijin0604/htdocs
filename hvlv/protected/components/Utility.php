<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class Utility {

    public static function randBase() {
        $base = array('2', '3', '4', '6', '7', '8', '9', 'C', 'B', 'D', 'F',
            'G', 'H', 'J', 'K', 'M', 'P', 'Q', 'R', 'T', 'V', 'W', 'X', 'Y');
        $strBuffer = "";
        $strNum = "";


        for ($i = 0; $i < 5; $i++) {
            $randomNum = rand(1, 23);
            $strBuffer .= $base[$randomNum];
            //strNum .=String.valueOf((int) base[randomNum]);
        }
        return $strBuffer;
    }

    /**
     * get open square root value
     * @param $num
     * @return float
     */
    public static function croot3($num)
    {
        $guess = $num / 3;
        while ( abs( $guess * $guess * $guess-$num) >= 0.0000000001 )
        {
            $guess = ( $num / $guess / $guess + 2 * $guess ) / 3;
        }
        return $guess;
    }

    public static function randomAlphaNum($length) {

        $rangeMin = pow(36, $length - 1); //smallest number to give length digits in base 36
        $rangeMax = pow(36, $length) - 1; //largest number to give length digits in base 36
        $base10Rand = mt_rand($rangeMin, $rangeMax); //get the random number
        $newRand = base_convert($base10Rand, 10, 36); //convert it

        return $newRand; //spit it out
    }
    
    public static function getCurrentDate(){
        return date('Y-m-d H:i:s');
    }
    
    public static function removeTimefromDate($dateTime){
        if(empty($dateTime)){
            return $dateTime;
        }
        
        $dt = explode(' ', $dateTime);
        return $dt[0];
        
    }


    /**
     * check to see if client is mobile device
     * @return bool
     */
    public static function isMobileRequest()
    {
        $_SERVER['ALL_HTTP'] = isset($_SERVER['ALL_HTTP']) ? $_SERVER['ALL_HTTP'] : '';
        $mobile_browser = '0';

        if (preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|iphone|ipad|ipod|android|xoom)/i', strtolower($_SERVER['HTTP_USER_AGENT'])))
            $mobile_browser++;
        if ((isset($_SERVER['HTTP_ACCEPT'])) and (strpos(strtolower($_SERVER['HTTP_ACCEPT']), 'application/vnd.wap.xhtml+xml') !== false))
            $mobile_browser++;
        if (isset($_SERVER['HTTP_X_WAP_PROFILE']))
            $mobile_browser++;
        if (isset($_SERVER['HTTP_PROFILE']))
            $mobile_browser++;

        $mobile_ua = strtolower(substr($_SERVER['HTTP_USER_AGENT'], 0, 4));
        $mobile_agents = array(
            'w3c ', 'acs-', 'alav', 'alca', 'amoi', 'audi', 'avan', 'benq', 'bird', 'blac',
            'blaz', 'brew', 'cell', 'cldc', 'cmd-', 'dang', 'doco', 'eric', 'hipt', 'inno',
            'ipaq', 'java', 'jigs', 'kddi', 'keji', 'leno', 'lg-c', 'lg-d', 'lg-g', 'lge-',
            'maui', 'maxo', 'midp', 'mits', 'mmef', 'mobi', 'mot-', 'moto', 'mwbp', 'nec-',
            'newt', 'noki', 'oper', 'palm', 'pana', 'pant', 'phil', 'play', 'port', 'prox',
            'qwap', 'sage', 'sams', 'sany', 'sch-', 'sec-', 'send', 'seri', 'sgh-', 'shar',
            'sie-', 'siem', 'smal', 'smar', 'sony', 'sph-', 'symb', 't-mo', 'teli', 'tim-',
            'tosh', 'tsm-', 'upg1', 'upsi', 'vk-v', 'voda', 'wap-', 'wapa', 'wapi', 'wapp',
            'wapr', 'webc', 'winw', 'winw', 'xda', 'xda-'
        );

        if (in_array($mobile_ua, $mobile_agents))
            $mobile_browser++;
        if (strpos(strtolower($_SERVER['ALL_HTTP']), 'operamini') !== false)
            $mobile_browser++;
        if (strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'windows') !== false)
            $mobile_browser = 0;
        if (strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'windows phone') !== false)
            $mobile_browser++;
        if ($mobile_browser > 0)
            return true;
        else
            return false;
    }

}

?>
