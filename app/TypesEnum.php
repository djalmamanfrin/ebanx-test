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

    public static function slugs(): array
    {
        return ['deposit', 'withdraw', 'transfer'];
    }

    public static function deposit(): string
    {
        return 'deposit';
    }

    public static function withdraw(): string
    {
        return 'withdraw';
    }

    public static function transfer(): string
    {
        return 'transfer';
    }
}
