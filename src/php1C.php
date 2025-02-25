<?php
declare(strict_types=1);

namespace Sikuda\Php1c;

class php1C
{
     /**
     * Запуск получения кода PHP
     *
     * @param string $buffer строка код для преобразования
     */
    function makeCode(string $buffer){

      $stream = new CodeStream();
      return $stream->makeCode($buffer);
    }
  
     /**
     * Запуск получения кода PHP
     *
     * @param string $buffer строка код для преобразования
     * @param string|null $name_var имя переменной для вывода результата выполнения кода
     */
    function evalCode(string $buffer, $name_var){

        $stream = new CodeStream();
        return $stream->makeCode($buffer, $name_var);
    }
}