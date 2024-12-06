<?php
declare(strict_types=1);

namespace Sikuda\Php1c\collections;

use const Sikuda\Php1c\fEnglishVariable;
use const Sikuda\Php1c\php1C_LetterEng;
use const Sikuda\Php1c\php1C_LetterLng;
use const Sikuda\Php1c\php1C_strFixedMap1C;

class FixedMap1C extends BaseCollection1C{
    function __construct($args=null,$copy=null){

        if(is_array($copy)) $this->value = $copy;
        else{
            $this->value = array();
            if( (count($args) > 0) && is_string($args[0])){
                $keys = explode(',',$args[0]);
                for ($i=0; $i < count($keys); $i++) {
                    $k = strtoupper(trim ($keys[$i]));
                    if( fEnglishVariable ) $k = str_replace(php1C_LetterLng, php1C_LetterEng, $k);
                    if(!isset($args[$i+1])) $this->value[$k] = null;
                    else $this->value[$k] = $args[$i+1];
                }
            }
        }
    }

    function __toString(){
        return php1C_strFixedMap1C;
    }
    /*
     * Для получения данных через точку
    */
    function Get($key){
        if( fEnglishVariable ) $key = str_replace(php1C_LetterLng, php1C_LetterEng, $key);
        $key = strtoupper($key);
        return $this->value[$key];
    }
}