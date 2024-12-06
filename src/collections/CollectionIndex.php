<?php
declare(strict_types=1);

namespace Sikuda\Php1c\collections;

/**
 * Индекс коллекции(пока пустая реализация для ТаблицыЗначений)
 */
class CollectionIndex{
    protected string $name;
    function __construct(string $col){
        $this->name = $col;
    }

    function __toString(){
        return php1C_strIndexCollection1C;
    }
}