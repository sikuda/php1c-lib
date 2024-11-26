<?php

namespace Sikuda\Php1c;
require_once( 'php1C_common.php');
class php1C
{
    /**
     * Запуск получения кода PHP
     *
     * @param string $buffer строка код для преобразования
     * @param string|null $name_var имя переменной для вывода результата выполнения кода
     */
    function makeCode(string $buffer, string $name_var = null){
        $stream = new CodeStream();
        return $stream->makeCode($buffer, $name_var);
    }

}