<?php

namespace Sikuda\Php1c\collections;

use Exception;
use Sikuda\Php1c\Number1C;
use const Sikuda\Php1c\php1C_strArray1C;



/**
 * Класс для работы с массивом 1С
 *
 */
class Array1C extends FixedArray1C {

    function __construct($counts=null, $copy=null){

        parent::__construct();
        if(is_array($copy)) $this->value = $copy;
        else{
            $this->value = array();
            if(is_array($counts) && (count($counts)>0)){
                if( count($counts) > 1 ) throw new Exception("Многомерные массивы пока не поддерживаются");
                $cnt = $counts[0];
                if( is_numeric($cnt) && $cnt > 0 ){
                    for ($i=0; $i < $cnt; $i++) $this->value[$i] = null;
                }
            }
        }
    }

    function __toString(): string {
        return php1C_strArray1C;
    }

    function Insert($index, $val){
        $index = $this->intIndex($index);
        if( isset($this->value[$index])){
            array_splice($this->value, $index, 0, $val);
        }
        else $this->value[$index] = $val;
    }

    function Add($val): Array1C
    {
        $this->value[] = $val;
        return $this;
    }

    function Clear(){
        unset($this->value);
        $this->value = array();
    }

    function Del($index){
        $index = $this->intIndex($index);
        unset($this->value[$index]);
    }

    function Set($index, $val){
        $index = $this->intIndex($index);
        $this->value[$index] = $val;
    }

    private function intIndex($index):int{
        if($index instanceof Number1C) $index = intval($index->getValue());
        return $index;
    }
}
