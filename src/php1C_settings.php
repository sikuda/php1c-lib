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

