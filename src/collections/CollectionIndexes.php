<?php

namespace Sikuda\Php1c\collections;

use Exception;
use const Sikuda\Php1c\fEnglishVariable;
use const Sikuda\Php1c\php1C_LetterEng;
use const Sikuda\Php1c\php1C_LetterLng;
use const Sikuda\Php1c\php1C_strIndexesCollection1C;

/**
 * Коллекция индексов(пока пустая реализация для ТаблицыЗначений)
 */
class CollectionIndexes{
    /**
     * @var array коллекция значений в строке
     */
    private $ValueTable;
    private array $Indexes;

    function __construct($parent){
        $this->ValueTable = &$parent;
        $this->Indexes = array();
    }

    function __toString(){
        return php1C_strIndexesCollection1C;
    }

    function toArray(): array
    {
        return $this->Indexes;
    }

    /**
     *  Добавляем колонку в коллекцию индексов
     *
     * @throws Exception
     */
    function Add($key): void
    {
        if(is_string($key)){
            if( fEnglishVariable ) $key = str_replace(php1C_LetterLng, php1C_LetterEng, $key);
            $key = strtoupper($key);
            $this->Indexes[$key] = new CollectionIndex($key);
        }
        else  throw new Exception("Имя колонки должно быть строкой");
    }

    function Count(): int
    {
        return count($this->Indexes);
    }

    function Clear(): void
    {
        unset($this->Indexes);
        $this->Indexes = array();
    }

    function Del($key): void
    {
        if( fEnglishVariable ) $key = str_replace(php1C_LetterLng, php1C_LetterEng, $key);
        $key = strtoupper($key);
        unset($this->Indexes[$key]);
    }
}
