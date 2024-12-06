<?php
declare(strict_types=1);

namespace Sikuda\Php1c\collections;


use Exception;
use const Sikuda\Php1c\php1C_UndefinedType;

class KeyAndValue1C{
    public  $key;
    public  $value;

    function __construct($key=php1C_UndefinedType,$value=php1C_UndefinedType){
        $this->key = $key;
        $this->value = $value;
    }

    /**
     * Возвращает ключ или значение
     * @throws Exception
     */
    function Get($key){
        switch ($key){
            case "КЛЮЧ":
            case "KLYUCH":
            case "KEY": return $this->key;
            case "ЗНАЧЕНИЕ":
            case "ZNACHENIE":
            case "VALUE": return $this->value;
        }
        throw new Exception("Не найден ключ или значение".$key);

    }
}
