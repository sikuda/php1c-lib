<?php

namespace Sikuda\Php1c\collections;

use Exception;
use const Sikuda\Php1c\fEnglishVariable;
use const Sikuda\Php1c\php1C_LetterEng;
use const Sikuda\Php1c\php1C_LetterLng;
use const Sikuda\Php1c\php1C_strMap1C;

/**
 * Класс для работы со структурой 1С
 */
class Map1C extends FixedMap1C {

    function __toString(){
        return php1C_strMap1C;
    }

    function Insert($key, $val=null){
        if( fEnglishVariable ) $key = str_replace(php1C_LetterLng, php1C_LetterEng, $key);
        $this->value[strtoupper($key)] = $val;
    }

    function Property($key, $value=null): bool
    {
        if( fEnglishVariable ) $key = str_replace(php1C_LetterLng, php1C_LetterEng, $key);
        $key = strtoupper($key);
        $value = $this->value[$key];
        return array_key_exists($key, $this->value);
    }

    function Clear(){
        unset($this->value);
        $this->value = array();
    }

    function Del($key){
        if( fEnglishVariable ) $key = str_replace(php1C_LetterLng, php1C_LetterEng, $key);
        $key = strtoupper($key);
        unset($this->value[$key]);
    }

    /**
     * Для установки данных через точку
     * @throws Exception
     */
    function Set($key, $val=null){
        if( fEnglishVariable ) $key = str_replace(php1C_LetterLng, php1C_LetterEng, $key);
        $key = strtoupper($key);
        if(array_key_exists($key, $this->value)) $this->value[$key] = $val;
        else throw new Exception("Не найден ключ структуры ".$key);
    }
}

