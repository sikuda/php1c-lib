<?php
declare(strict_types=1);

namespace Sikuda\Php1c\collections;

/**
 * Базовый класс коллекций 1С
 */
class BaseCollection1C{
    public array $value;

    function getItem($key, $value){
        return $value;
    }

    function toArray(): array{
        return $this->value;
    }
    function Count(): int
    {
        return count($this->value);
    }
}
