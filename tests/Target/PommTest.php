<?php

declare(strict_types=1);

namespace Tests\RulerZ\Target;

use PHPUnit\Framework\TestCase;
use RulerZ\Compiler\CompilationTarget;
use RulerZ\Compiler\Context;
use RulerZ\Model\Executor;
use RulerZ\Model\Rule;
use RulerZ\Parser\Parser;
use RulerZ\Pomm\Target\Pomm;
use Tests\RulerZ\Stub\ModelStub;

class PommTest extends TestCase
{
    private $target;

    public function setUp()
    {
        $this->target = new Pomm();
    }

    /**
     * @dataProvider supportedTargetsAndModes
     */
    public function testSupportedTargetsAndModes($target, string $mode): void
    {
        $this->assertTrue($this->target->supports($target, $mode));
    }

    public function supportedTargetsAndModes(): array
    {
        $model = new ModelStub();

        return [
            [$model, CompilationTarget::MODE_APPLY_FILTER],
            [$model, CompilationTarget::MODE_FILTER],
            [$model, CompilationTarget::MODE_SATISFIES],
        ];
    }

    /**
     * @dataProvider unsupportedTargets
     */
    public function testItRejectsUnsupportedTargets($target)
    {
        $this->assertFalse($this->target->supports($target, CompilationTarget::MODE_FILTER));
    }

    public function unsupportedTargets(): array
    {
        return [
            ['string'],
            [42],
            [new \stdClass()],
            [[]],
        ];
    }

    public function testItReturnsAnExecutorModel()
    {
        $rule = '1 = 1';

        /** @var Executor $executorModel */
        $executorModel = $this->target->compile($this->parseRule($rule), new Context());

        $this->assertInstanceOf(Executor::class, $executorModel);

        $this->assertCount(2, $executorModel->getTraits());
        $this->assertSame('(new \PommProject\Foundation\Where("1 = 1", []))', $executorModel->getCompiledRule());
    }

    public function testItSupportsParameters()
    {
        $rule = 'points > :nb_points and group IN [:admin_group, :super_admin_group]';

        /** @var Executor $executorModel */
        $executorModel = $this->target->compile($this->parseRule($rule), new Context());

        $this->assertSame('(new \PommProject\Foundation\Where("points > $*", [$parameters["nb_points"]]))->andWhere((new \PommProject\Foundation\Where("group IN ($*, $*)", [$parameters["admin_group"], $parameters["super_admin_group"]])))', $executorModel->getCompiledRule());
    }

    public function testItSupportsCustomOperators()
    {
        $rule = 'points > 30 and always_true()';

        $this->target->defineOperator('always_true', function () {
            throw new \LogicException('should not be called');
        });

        /** @var Executor $executorModel */
        $executorModel = $this->target->compile($this->parseRule($rule), new Context());

        $this->assertSame('(new \PommProject\Foundation\Where("points > 30", []))->andWhere((new \PommProject\Foundation\Where(call_user_func($operators["always_true"]), [])))', $executorModel->getCompiledRule());
    }

    public function testItSupportsInlineOperators()
    {
        $rule = 'points > 30 and always_true()';

        $this->target->defineInlineOperator('always_true', function () {
            return '1 = 1';
        });

        /** @var Executor $executorModel */
        $executorModel = $this->target->compile($this->parseRule($rule), new Context());

        $this->assertSame('(new \PommProject\Foundation\Where("points > 30", []))->andWhere((new \PommProject\Foundation\Where("1 = 1", [])))', $executorModel->getCompiledRule());
    }

    public function testItImplicitlyConvertsUnknownOperators()
    {
        $rule = 'points > 30 and always_true()';

        /** @var Executor $executorModel */
        $executorModel = $this->target->compile($this->parseRule($rule), new Context());

        $this->assertSame('(new \PommProject\Foundation\Where("points > 30", []))->andWhere((new \PommProject\Foundation\Where("always_true()", [])))', $executorModel->getCompiledRule());
    }

    private function parseRule(string $rule): Rule
    {
        return (new Parser())->parse($rule);
    }
}
