<?php
declare(strict_types=1);

namespace Sikuda\Php1c\collections;
use Exception;
use Sikuda\Php1c\Number1C;

class FixedArray1C extends BaseCollection1C {

    /**
     * @throws Exception
     */
    function __construct($array1C=null){
        if (is_null($array1C)) return;
        elseif(isset($array1C[0]) && $array1C[0] instanceof Array1C) $this->value = $array1C[0]->toArray();
        else throw new Exception("Неправильный конструктор ФиксированногоМассива");
    }

    function __toString(): string {
        return php1C_strFixedArray1C;
    }

    function UBound(): int
    {
        return max(array_keys($this->value));
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    function Find($val){
        $key = array_search($val, $this->value);
        if($key === FALSE) return php1C_UndefinedType;
        else {
            if(is_numeric($key)) return new Number1C(strval($key));
            else return $key;
        }
    }

    function Get($index){
        $index = $this->intIndex($index);
        return $this->value[$index];
    }
    private function intIndex($index):int{
        if($index instanceof Number1C) $index = intval($index->getValue());
        return $index;
    }
}

