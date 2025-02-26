<?php
declare(strict_types=1);

namespace Sikuda\Php1c\collections;

use const Sikuda\Php1c\php1C_strColumnValueTable1C;

/**
 * Класс колонки таблицы значений 1С
 *
 */
class ValueTableColumn1С{

    /**
     * @var array коллекция значений в колонке
     */
    public $NAME;

    function __construct($val=null){
        $this->NAME = $val;
        //$this->ИМЯ  = &$this->NAME;
    }

    function __toString(){
        return php1C_strColumnValueTable1C;
    }

}