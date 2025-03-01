<?php
declare(strict_types=1);

namespace Sikuda\Php1c\collections;

use Exception;
use const Sikuda\Php1c\fEnglishVariable;
use const Sikuda\Php1c\php1C_LetterEng;
use const Sikuda\Php1c\php1C_LetterLng;

/**
 * Класс для работы с таблицей значений 1С8
 *
 */
class ValueTable1С {

    private array $rows;   //array of ValueTableRow
    public ValueTableColumnCollection1С $COLUMNS; //ValueTableColumnCollection - collection of ValueTableColumn
    //public $КОЛОНКИ;
    public $KOLONKI;
    public CollectionIndexes1С $INDEXES; //CollectionIndexes коллекция из CollectionIndex
    //public $ИНДЕКСЫ;
    public $INDEKSYY;

    function __construct($args=null,$copy=null){

        if(is_array($copy)) $this->rows = $copy;
        else{
            $this->rows = array();
            $this->COLUMNS = new ValueTableColumnCollection1С($this);
            //$this->КОЛОНКИ = &$this->COLUMNS;
            $this->KOLONKI = &$this->COLUMNS;
            $this->INDEXES = new CollectionIndexes1С($this);
            //$this->ИНДЕКСЫ = &$this->INDEXES;
            $this->INDEKSYY = &$this->INDEXES;
        }
    }

    function __toString(){
        return php1C_strValueTable1C;
    }

    function toArray(): array{
        return $this->rows;
    }

    /**
     * Возвратить элемент для каждого
     */
    function getItem($key, $value){
        return $value;
    }

    /**
     * Возвратить количество строк
     */
    function Count(): int{
        return count($this->rows);
    }

    //Добавить новую строку в таблицу
    function Add(): ValueTableRow1С {
        $row = new ValueTableRow1С($this);
        $this->rows[] = $row;
        return $row;
    }

    /**
     * Вставить новую строку в таблицу
     * @throws Exception
     */
    function Insert($index): ValueTableRow1С
    {
        if(is_int($index)){
            $row = new ValueTableRow1С($this);
            $this->rows[$index] = $row;
            return $row;
        }
        else  throw new Exception("Индекс задан неверно");
    }

    /**
     * Выгрузка колонки в Array1C
     * @throws Exception
     */
    function UnloadColumn($col): Array1C
    {
        $array = new Array1C;
        if(is_int($col)){
            $col = $this->COLUMNS->cols[$col];
        }elseif (is_string($col)) {
            if( fEnglishVariable ) $col = str_replace(php1C_LetterLng, php1C_LetterEng, $col);
            $col = $this->COLUMNS->cols[strtoupper($col)];
        }
        else throw new Exception("Не задана колонка для выгрузки ".$col);
        if(is_object($col) && get_class($col) === 'ValueTableColumn'){
            foreach ($this->rows as $key => $value) {
                $val = $value->Get($col->NAME);
                $array->Add($val);
            }
        }
        return $array;
    }

    /**
     * Загрузка колонки из Array1C
     * @throws Exception
     */
    function LoadColumn($arr, $col){
        if($arr instanceof Array1C)
            throw new Exception("Первый аргумент должен быть массивом ".$arr);
        if(isset($col)){
            if(is_int($col)){
                $col = $this->COLUMNS->cols[$col];
            }elseif (is_string($col)) {

                $col = $this->COLUMNS->cols[strtoupper($col)];
            }
            if($col instanceof ValueTableColumn1С){
                $k = 0;
                foreach ($this->rows as $key => $value) {
                    $value->Set($col->NAME, $arr[$k]);
                    $k++;
                }
                return;
            }
        }
        throw new Exception("Не найдена колонка для загрузки ".$col);
    }

    /**
     * Заполним имя всех столбцов
     */
    function GetAllColumns(){
        $strCols = '';
        foreach ($this->COLUMNS->cols as $val) {
            $strCols .= $val->NAME.',';
        }
        return substr($strCols,0,-1); //уберем последнюю запятую
    }

    /**
     * Заполнить значениями таблицу
     */
    function FillValues($value, string $strCols=null){
        if(!isset($strCols)) $strCols = $this->GetAllColumns();
        if($strCols===false) return;
        if( fEnglishVariable ) $strCols = str_replace(php1C_LetterLng, php1C_LetterEng, $strCols);
        $keys = explode(',',$strCols);
        for ($i=0; $i < count($keys); $i++){
            $col = strtoupper(trim($keys[$i]));
            foreach ($this->rows as $val) {
                $val->Set($col, $value);
            }
        }
    }

    /**
     * Возвратить индекс строки в таблице
     */
    function IndexOf($row){
        $key = array_search( $row, $this->rows);
        if( $key === FALSE ) $key = -1;
        return $key;
    }

    /**
     * Возвратить итог по колонке
     * @throws Exception
     */
    function Total($col){
        if( fEnglishVariable ) $col = str_replace(php1C_LetterLng, php1C_LetterEng, $col);
        $col = strtoupper($col);
        $sum = 0;
        foreach ($this->rows as $value) {
            $val = $value->Get($col);
            if(is_numeric($val) || $val instanceof Number1C){
                $sum = add1C($sum, $val);
            }
        }
        return $sum;
    }

    /**
     * Найти значение в таблице и возвращать строку или Неопределенно
     */
    function Find($value, $strCols=null){
        if(!isset($strCols)) $strCols = $this->GetAllColumns();
        $keys = explode(',',$strCols);
        for ($i=0; $i < count($keys); $i++){
            $col = strtoupper(trim($keys[$i]));
            foreach ($this->rows as $row) {
                $val = $row->Get($col);
                if( $val === $value ) return $row;
            }
        }
        return php1C_UndefinedType;
    }

    /**
     * Поиск по структуре возврат Array1C
     * @throws Exception
     */
    function FindRows($filter): Array1C
    {
        if($filter instanceof Structure1C){
            throw new Exception("Аргумент функции должен быть структурой ".$filter);
        }
        $array_filter = $filter->toArray();
        $array = new Array1C();
        foreach ($this->rows as $key => $row){
            $found = true;
            foreach ($array_filter as $key_filter => $value_filter) {
                if( $row[$key_filter] == $value_filter ){
                    $found = false;
                }
            }
            if($found) $array->Add($row);
        }
        return $array;
    }

    /**
     * Очистить значения таблицы
     */
    function Clear(){
        $this->COLUMNS->setValueTable(null);
        unset($this->rows);
        $this->rows = array();
    }

    /**
     * Для получения данных через точку
     * @throws Exception
     */
    function Get($key){
        if(is_string($key)){
            if( fEnglishVariable ) $key = str_replace(php1C_LetterLng, php1C_LetterEng, $key);
            $key = strtoupper($key);
            if($key === 'КОЛОНКИ' || $key === 'COLUMNS' || $key === 'KOLONKI'){
                return $this->COLUMNS;
            }
        }
        if(is_numeric($key)){
            return $this->rows[$key];
        }
        throw new Exception("Не найден ключ для строки ТаблицыЗначений ".$key);
    }
    /**
     * Для установки данных через точку
     * @throws Exception
     */
    function Set($key, ValueTableColumnCollection1С $val){
        if(is_string($key)){
            if( fEnglishVariable ) $key = str_replace(php1C_LetterLng, php1C_LetterEng, $key);
            $key = strtoupper($key);
            if(($key === 'КОЛОНКИ' || $key === 'COLUMNS') && (get_class($val) === 'ValueTableColumnCollection')){
                $this->COLUMNS = $val;
                $this->COLUMNS->setValueTable($this);
            }
        }
        if(is_numeric($key) && (get_class($val) === 'ValueTableRow')){
            $this->rows[$key] = $val;
        }
        throw new Exception("Не найден имя столба ТаблицыЗначений ".$key);
    }

    //Группируем данные таблицы значений
    /**
     * @throws Exception
     */
    function GroupBy(string $colGr, string $colSum){
        if( fEnglishVariable ) $colGr = str_replace(php1C_LetterLng, php1C_LetterEng, $colGr);
        if( fEnglishVariable ) $colSum = str_replace(php1C_LetterLng, php1C_LetterEng, $colSum);
        $grKeys = explode(',',$colGr);
        $sumKeys = explode(',',$colSum);
        $table = $this->CopyColumns($colGr.','.$colSum);
        $this->COLUMNS = $table->COLUMNS;
        $this->COLUMNS->setValueTable($this);
        foreach ($this->rows as $row) {

            //Поиск совпадений по группировке
            $fNew = true;
            foreach ($table->rows as $newRow){
                $found = true;
                foreach ($grKeys as $grKey){
                    if($newRow->Get($grKey) != $row->Get($grKey)){
                        $found = false;
                        break;
                    }
                }
                if($found){
                    $fNew = false;
                    break;
                }
            }

            if($fNew){
                //новая строка
                $newRow = $table->Add($this);
                $newRow->setValueTable($this);
                foreach ($grKeys as $grkey){
                    $newRow->Set($grkey, $row->Get($grkey));
                }
                foreach ($sumKeys as $sumkey){
                    $newRow->Set($sumkey, $row->Get($sumkey));
                }
            }else{
                //суммируем данные в строку
                foreach ($sumKeys as $sumkey){
                    $curr = $newRow->Get($sumkey);
                    $newRow->Set($sumkey, $curr + $row->Get($sumkey));
                }
            }
        }
        unset($this->rows);
        $this->rows = $table->rows;
        unset($table);
    }

    //Сдвинуть строку $row на $offset
    function Move($row, $offset){
        if(is_object($row) && get_class($row) === 'ValueTableRow'){
            $row = $this->IndexOf($row);
        }
        $row_int = intval($row);
        $offset_int = intval($offset);
        $row_object = $this->rows[$row_int];
        array_splice($this->rows,$row_int,1);
        array_splice($this->rows,$row_int+$offset_int,0,array($row_object));
    }

    /**
     * Скопировать таблицуЗначений с фильтрацией по строкам и колонкам
     *
     * @param null $rows массив строк для выгрузки
     * @param string|null $strcols
     * @return ValueTable1С - возвращает новый объект ТаблицаЗначений1С
     * @throws Exception
     */
    function Copy($rows=null, string $strcols=null): ValueTable1С
    {
        if(isset($row) && (!is_object($rows) || get_class($rows) !== 'Array1C')) throw new Exception("Первый параметр должен быть массивом строк или пустым");
        if(!isset($strcols)) $strcols = $this->GetAllColumns();
        if( fEnglishVariable ) $strcols = str_replace(php1C_LetterLng, php1C_LetterEng, $strcols);
        $array = $this->CopyColumns($strcols);
        if(!isset($rows)) $rows = $this->rows;
        else $rows = $rows->toArray();
        foreach ($rows as $row){
            $newRow = $array->Add();
            foreach ($array->COLUMNS->cols as $col){
                //var_dump($col);
                $newRow->Set($col->NAME, $row->Get($col->NAME));
            }
        }
        return $array;
    }

    /**
     * Скопировать пустые колонки ТаблицуЗначений в новую ТаблицуЗначений
     *
     * @param string $strCols строка перечисления колонок
     * @return ValueTable1С - возвращает новый объект ТаблицаЗначений1С
     * @throws Exception
     */
    function CopyColumns(string $strCols): ValueTable1С
    {
        if(!isset($strCols)) $strCols = $this->GetAllColumns();
        if( fEnglishVariable ) $strCols = str_replace(php1C_LetterLng, php1C_LetterEng, $strCols);
        $array = new ValueTable1С;
        $keys = explode(',',$strCols);
        for ($i=0; $i < count($keys); $i++){
            $col = strtoupper(trim($keys[$i]));
            $array->COLUMNS->Add($col);
        }
        return $array;
    }

    /**
     * Отсортировать таблицу значений по стоке с колонками
     *
     * @param string $strolls @strcols string строка перечислений колонов и порядка сортировки ("Товар, Цена Убыв")
     * @param @cmp_object объект сортировки
     * @throws Exception
     */
    function Sort(string $strolls, $cmp_object=null){

        if (isset($cmp_object)) throw new Exception("Пока нет реализации по объекту сравнения");

        if(!isset($strolls)) $strolls = $this->GetAllColumns();
        if( fEnglishVariable ) $strolls = str_replace(php1C_LetterLng, php1C_LetterEng, $strolls);
        if(!is_string($strolls)) throw new Exception("Первый параметр должен быть обязаельно заполнен наименованиями колонок");
        $Sort = array();
        $Sorted = array();
        $pairs = explode(',',$strolls);
        foreach ($pairs as $pair) {
            $keys = explode(' ',$pair);
            if ($keys[0] === false) $col = "";
            else $col = strtoupper(trim($keys[0]));
            if ($keys[1] === false) $colder = "";
            else $colder = strtoupper(trim($keys[1]));
            if($colder==='УБЫВ' || $colder==="DESC") $Sorted[] =-1;
            else $Sorted[] = 1;
            $Sort[] = $col;
        }
        usort($this->rows, function($a, $b) use ($Sorted, $Sort) {
            for($i=0;$i<count($Sort);$i++){
                $vala = $a->Get($Sort[$i]);
                $vale = $b->Get($Sort[$i]);
                if($vala !== $vale) return $Sorted[$i] *(($vala < $vale) ? -1 : 1);
            }
            return 0;
        });
        unset($this->sortdir);
        unset($this->sort);
    }

    /**
     * Удалить строку из таблицы
     * @throws Exception
     */
    function Del($row){
        if(is_int($row)){
            $row = $this->rows[$row];
        }elseif(!is_object($row) && get_class($row) !== 'ValueTableRow'){
            throw new Exception("Параметр может быть либо строкой либо числом");
        }
        $key = $this->IndexOf($row);
        if($key !== -1){
            $row->setValueTable(null);
            unset($this->rows[$key]);
        }
    }
}

