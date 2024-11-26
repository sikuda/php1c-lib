<?php

namespace Sikuda\Php1c\collections;

use Exception;
use const Sikuda\Php1c\fEnglishVariable;
use const Sikuda\Php1c\php1C_LetterEng;
use const Sikuda\Php1c\php1C_LetterLng;
use const Sikuda\Php1c\php1C_strStructure1C;



/**
 * Класс для работы со структурой 1С
 */
class Structure1C extends FixedStructure1C {

    function __toString(){
        return php1C_strStructure1C;
    }

    function Insert($key, $val=null){
        $key2 = $key;
        if(is_string($key)){
            if( fEnglishVariable ) $key2 = str_replace(php1C_LetterLng, php1C_LetterEng, $key);
            $key2 = trim(mb_strtoupper($key2));
        }
        $this->value[$key2] = $val;
        $this->keysOrigin[$key2] = $key;
    }

    /**
     * Есть свойство
     * @param $key - ключ
     * @param $value - значение
     * @return bool - Истина если ключ(значение) существует
     */
    function Property($key, &$value=null): bool{
        if (is_string($key)) {
            $key = mb_strtoupper($key);
            if (fEnglishVariable) $key = str_replace(php1C_LetterLng, php1C_LetterEng, $key);
        }
        $value = $this->value[$key];
        return array_key_exists($key, $this->value);
    }

    function Clear(){
        unset($this->value);
        unset($this->keysOrigin);
        $this->value = array();
        $this->keysOrigin = array();
    }

    function Del($key){
        if( fEnglishVariable ) $key = str_replace(php1C_LetterLng, php1C_LetterEng, $key);
        $key = mb_strtoupper($key);
        unset($this->value[$key]);
        unset($this->keysOrigin[$key]);
    }

    /**
     * Для установки данных через точку
     * @throws Exception
     */
    function Set($key, $val=null){
        $key2 = $key;
        if(is_string($key)) {
            $key2 = mb_strtoupper($key2);
            if (fEnglishVariable) $key2 = str_replace(php1C_LetterLng, php1C_LetterEng, $key);
        }
        if(array_key_exists($key2, $this->value)){
            $this->value[$key2] = $val;
            $this->keysOrigin[$key2] = $key;
        }
        else throw new Exception("Не найден ключ структуры ".$key);
    }
}
