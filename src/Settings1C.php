<?php
declare(strict_types=1);

namespace Sikuda\Php1c;
// Установка языка программирования
//- ru - default setting
//- en
if (!defined('Language1C')) define('Language1C', 'ru');

//true - Использовать только латинские переменные в PHP (false - переменные не переводятся)
const fEnglishVariable = true;

//true - Использовать только английские названия типов, false - не переводятся
const fEnglishTypes = true;

const Scale1C = 36;
const Scale1C_Int = 27;

// Группировка 12 345.66 или 12345.66 
const Regional_grouping = false;

//id уникальные от oscript
const php1C_UndefinedType = 'Undefined-783CE532-8CE0-4C59-BEF4-835AEFB715E4';
const php1C_NullType = 'Null-26D78088-915A-4294-97E1-FB39E70187A6';

