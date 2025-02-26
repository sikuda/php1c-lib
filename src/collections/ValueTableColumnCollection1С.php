<?php
declare(strict_types=1);

namespace Sikuda\Php1c\collections;

use Exception;
use Sikuda\Php1c\ValueTableColumn;
use const Sikuda\Php1c\fEnglishVariable;
use const Sikuda\Php1c\php1C_strColumnsValueTable1C;

/**
 * Класс коллекции колонок таблицы значений 1С
 *
 */
class ValueTableColumnCollection1С{

    /**
     * @var array коллекция ValueTableColumn
     */
    private $ValueTable;
    public array $cols;

    function __construct($parent){
        $this->ValueTable = &$parent;
        $this->cols = array();
    }

    function toArray(): array
    {
        return $this->cols;
    }

    function setValueTable($parent){
        $this->ValueTable = &$parent;
    }

    function __toString(){
        return php1C_strColumnsValueTable1C;
    }

    /**
     * @throws Exception
     */
    function Add($key=null){
        if(!isset($key)) $key = ''; //пустые имена колонок в 1С допустимы.
        if(is_string($key)){
            if( fEnglishVariable ) $key = str_replace(php1C_LetterLng, php1C_LetterEng, $key);
            $key = strtoupper($key);
            $this->cols[$key] = new ValueTableColumn($key);
        }
        else  throw new Exception("Имя колонки должно быть строкой");
    }

    function Count(): int {
        return count($this->cols);
    }
}

