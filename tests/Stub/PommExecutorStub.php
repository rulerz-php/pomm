<?php

declare(strict_types=1);

namespace Tests\RulerZ\Stub;

use RulerZ\Pomm\Executor\FilterTrait;

class PommExecutorStub
{
    public static $executeReturn;

    use FilterTrait;

    public function execute($target, array $operators, array $parameters)
    {
        return self::$executeReturn;
    }
}
