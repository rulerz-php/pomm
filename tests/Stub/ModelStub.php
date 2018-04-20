<?php

declare(strict_types=1);

namespace Tests\RulerZ\Stub;

use PommProject\ModelManager\Model\ModelTrait\ReadQueries;
use PommProject\ModelManager\Model\Model;

class ModelStub extends Model
{
    use ReadQueries;

    public function findCustom()
    {
    }
}
