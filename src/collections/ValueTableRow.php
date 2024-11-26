<?php

namespace Sikuda\Php1c\collections;

use Exception;
use const Sikuda\Php1c\fEnglishVariable;
use const Sikuda\Php1c\php1C_LetterEng;
use const Sikuda\Php1c\php1C_LetterLng;
use const Sikuda\Php1c\php1C_strRowValueTable1C;

/**
 * Класс строки для таблицы значений 1С
 *
 */
class ValueTableRow{

    /**
     * @var array коллекция значений в строке
     */
    private ValueTable $ValueTable; //parent
    private $row;        //array of fields

    function __construct($args=null){
        if(isset($args)) $this->ValueTable = &$args;
        $this->row = array();
    }

    function __toString(){
        return php1C_strRowValueTable1C;
    }

    function setValueTable($parent){
        if ($parent === null) unset($this->ValueTable);
        else $this->ValueTable = &$parent;
    }

    //Для получения данных через точку

    /**
     * @throws Exception
     */
    function Get($key){
        if(is_string($key)){
            if( fEnglishVariable ) $key = str_replace(php1C_LetterLng, php1C_LetterEng, $key);
            $key = strtoupper($key);
            $array = $this->ValueTable->COLUMNS->cols;
            if(array_key_exists($key, $array)){
                $key = strtoupper($key);
                return $this->row[$key];
            }
        }
        throw new Exception("Поле объекта не обнаружено у строки таблицы ".$key);
    }

    //Для установки данных через точку

    /**
     * @throws Exception
     */
    function Set($key, $value=null){
        if(is_string($key)){
            if( fEnglishVariable ) $key = str_replace(php1C_LetterLng, php1C_LetterEng, $key);
            $key = strtoupper($key);
            $this->row[$key] = $value;
        }
        else throw new Exception("Нет такой колонки в таблице");
    }
}
