<?php

namespace App;

use ReflectionClass;

class TypesEnum
{
    const DEPOSIT_ID = 1;
    const WITHDRAW_ID = 2;
    const TRANSFER_ID = 3;

    public static function all(): array
    {
        $reflector = new ReflectionClass(__CLASS__);
        return $reflector->getConstants();
    }
}
