<?php

declare(strict_types=1);

namespace Tests\RulerZ\Executor;

use PHPUnit\Framework\TestCase;
use PommProject\Foundation\Where;
use RulerZ\Context\ExecutionContext;
use Tests\RulerZ\Stub\ModelStub;
use Tests\RulerZ\Stub\PommExecutorStub;

class FilterTraitTest extends TestCase
{
    /** @var PommExecutorStub */
    private $executor;

    public function setUp()
    {
        $this->executor = new PommExecutorStub();
    }

    public function testItCanApplyAFilterOnATarget()
    {
        $modelStub = $this->createMock(ModelStub::class);
        $whereClause = new Where();

        $modelStub->expects($this->never())->method('findWhere');

        PommExecutorStub::$executeReturn = $whereClause;

        $filteretTarget = $this->executor->applyFilter($modelStub, $parameters = [], $operators = [], new ExecutionContext());

        $this->assertSame($whereClause, $filteretTarget, 'The trait is called and it returns the result generated by the executor');
    }

    public function testItCallsFindWhereOnTheTarget()
    {
        $modelStub = $this->createMock(ModelStub::class);
        $whereClause = new Where();

        $results = ['result'];

        $modelStub->expects($this->once())
            ->method('findWhere')
            ->with($whereClause)
            ->willReturn($results);

        PommExecutorStub::$executeReturn = $whereClause;

        $filteredResults = $this->executor->filter($modelStub, $parameters = [], $operators = [], new ExecutionContext());

        $this->assertInstanceOf(\Traversable::class, $filteredResults, 'Executors always return traversable objects');
        $this->assertSame(iterator_to_array($filteredResults), $results);
    }

    public function testItCallsACustomMethodIfSpecifiedInTheContext()
    {
        $modelStub = $this->createMock(ModelStub::class);
        $whereClause = new Where();

        $results = ['result'];

        $modelStub->expects($this->once())
            ->method('findCustom')
            ->with($whereClause)
            ->willReturn($results);

        PommExecutorStub::$executeReturn = $whereClause;

        $filteredResults = $this->executor->filter($modelStub, $parameters = [], $operators = [], new ExecutionContext([
            'method' => 'findCustom',
        ]));

        $this->assertInstanceOf(\Traversable::class, $filteredResults, 'Executors always return traversable objects');
        $this->assertSame(iterator_to_array($filteredResults), $results);
    }
}
