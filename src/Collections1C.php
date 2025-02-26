<?php
declare(strict_types=1);

/**
* Модуль работы с универсальными коллекциями значений 1С
* 
* Модуль для работы с массивами, структурами в 1С и функциями для работы с ними
* (соответствиями, списком значений, таблица значений)
* 
* @author  sikuda@yandex.ru
* @version 0.3
*/
namespace Sikuda\Php1c;
use Exception;
use Sikuda\Php1c\collections\Array1C;
use Sikuda\Php1c\collections\FixedArray1C;
use Sikuda\Php1c\collections\Map1C;
use Sikuda\Php1c\collections\Structure1C;
use Sikuda\Php1c\collections\ValueTable1С;

//require_once('TokenStream.php');

/**
* Массив названий типов для работы с коллекциями переименовании
*/
const php1C_typesPHP_Collection = array('Array1C','Structure1C','Map1C','ValueTable1C', 'FixedArray1C');

/**
* Массив названий английских функций для работы с датой. Соответствует элементам русским функций.
* @return string[] Массив названий функций работы с датой.
*/   
const php1C_functionsPHP_Collections = array('UBound(',   'Insert(',   'Add(',      'Count(',      'Find(',  'Clear('  , 'Get(',      'Del(',    'Set(',       'Property(','LoadColumn(',     'UnloadColumn(',      'FillValues(',      'IndexOf(','Total(','Find(','FindRows(',    'Clear(',   'GroupBy(',  'Move(',    'Copy(',       'CopyColumns(',          'Sort(',       'Del(');

/**
 * Вызывает функции и функции объектов 1С работы с коллекциями
 *
 * @param string $key строка в названии функции со скобкой
 * @param array $arguments аргументы функции в массиве
 * @return Structure1C|Map1C|Array1C|ValueTable1С|FixedArray1C результат функции или выбрасывает исключение
 * @throws Exception
 */
function callCollectionType(string $key, array $arguments)
{
    switch($key) {
        case 'Array1C': return Array1C($arguments);
        case 'FixedArray1C': return FixedArray1C($arguments);
        case 'Structure1C': return Structure1C($arguments);
        case 'Map1C': return Map1C($arguments);
        case 'ValueTable1C': return ValueTable1C($arguments);
        default: throw new Exception('Пока тип в коллекциях не определен ' . $key);
    }
}


/**
 * @throws Exception
 */
function Array1C($args): Array1C
{
    return new Array1C($args);
}

/**
 * @throws Exception
 */
function FixedArray1C($args): FixedArray1C
{
    return new FixedArray1C($args);
}

/**
 * Получение структуры 1С
 *
 * @param array|null $args аргументы функции в массиве
 * @return Structure1C - возвращает новый объект массива 1С
 *
 */
function Structure1C(array $args=null): Structure1C
{
    return new Structure1C($args);
}

/**
 * Получение соответствия 1С
 *
 * @param $args
 * @return Map1C - возвращает новый объект массива 1С
 *
 */
function Map1C($args=null): Map1C
{
	return new Map1C($args);
}

//----------------------------------------------------------------------------------------------

/**
 * Получение ТаблицыЗначений
 *
 * @param $args аргументы функции в массиве
 * @return ValueTable1С - возвращает новый объект ТаблицаЗначений1С
 *
 */
function ValueTable($args=null): ValueTable1С
{
	return new ValueTable1С($args);
}






