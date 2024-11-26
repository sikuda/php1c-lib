<?php

namespace Sikuda\Php1c\collections;

use const Sikuda\Php1c\fEnglishVariable;
use const Sikuda\Php1c\php1C_LetterEng;
use const Sikuda\Php1c\php1C_LetterLng;
use const Sikuda\Php1c\php1C_strFixedStructure1C;
use const Sikuda\Php1c\php1C_UndefinedType;

/**
 * Класс для работы с фиксированной структурой 1С
 */
class FixedStructure1C extends BaseCollection1C{
    protected array $keysOrigin;

    function __construct($args=null,$copy=null){

        if(is_array($copy)) $this->value = $copy;
        else{
            $this->value = array();
            if( (count($args) > 0) && is_string($args[0])){
                $keys = explode(',',$args[0]);
                for ($i=0; $i < count($keys); $i++) {
                    $k = mb_strtoupper(trim ($keys[$i]));
                    if( fEnglishVariable ) $k = str_replace(php1C_LetterLng, php1C_LetterEng, $k);
                    if(!isset($args[$i+1]))
                        $this->value[$k] = php1C_UndefinedType;
                    else
                        $this->value[$k] = $args[$i+1];
                    $this->keysOrigin[$k] = $keys[$i];
                }
            }
        }
    }
    function __toString(){
        return php1C_strFixedStructure1C;
    }
    function getItem($key, $value): KeyAndValue1C
    {
        return new KeyAndValue1C($this->keysOrigin[$key], $value);
    }
    //Для получения данных через точку
    function Get($key){
        if(is_string($key)){
            if( fEnglishVariable ) $key = str_replace(php1C_LetterLng, php1C_LetterEng, $key);
            $key = mb_strtoupper($key);
        }
        return $this->value[$key];
    }
}
