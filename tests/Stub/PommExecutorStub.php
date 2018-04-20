<?php

namespace Tests\RulerZ\Stub;

use RulerZ\Executor\Pomm\FilterTrait;

class PommExecutorStub
{
    public static $executeReturn;

    use FilterTrait;

    public function execute($target, array $operators, array $parameters)
    {
        return self::$executeReturn;
    }
}
