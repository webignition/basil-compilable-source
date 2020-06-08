<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Tests\Unit\Line;

use webignition\BasilCompilableSource\Expression\ComparisonExpression;
use webignition\BasilCompilableSource\Expression\ExpressionInterface;
use webignition\BasilCompilableSource\Expression\LiteralExpression;
use webignition\BasilCompilableSource\MethodInvocation\ObjectMethodInvocation;
use webignition\BasilCompilableSource\Metadata\Metadata;
use webignition\BasilCompilableSource\Metadata\MetadataInterface;
use webignition\BasilCompilableSource\VariableDependency;
use webignition\BasilCompilableSource\VariableDependencyCollection;

class ComparisonExpressionTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider createDataProvider
     */
    public function testCreate(
        ExpressionInterface $leftHandSide,
        ExpressionInterface $rightHandSide,
        string $comparison,
        MetadataInterface $expectedMetadata
    ) {
        $expression = new ComparisonExpression($leftHandSide, $rightHandSide, $comparison);

        $this->assertEquals($expectedMetadata, $expression->getMetadata());
        $this->assertSame($leftHandSide, $expression->getLeftHandSide());
        $this->assertSame($rightHandSide, $expression->getRightHandSide());
        $this->assertSame($comparison, $expression->getComparison());
    }

    public function createDataProvider(): array
    {
        return [
            'no metadata' => [
                'leftHandSide' => new LiteralExpression('5'),
                'rightHandSide' => new LiteralExpression('6'),
                'comparison' => '===',
                'expectedMetadata' => new Metadata(),
            ],
            'has metadata' => [
                'leftHandSide' => new ObjectMethodInvocation(
                    new VariableDependency('DEPENDENCY'),
                    'methodName'
                ),
                'rightHandSide' => new LiteralExpression('literal'),
                'comparison' => '!==',
                'expectedMetadata' => new Metadata([
                    Metadata::KEY_VARIABLE_DEPENDENCIES => new VariableDependencyCollection([
                        'DEPENDENCY',
                    ])
                ]),
            ],
        ];
    }

    /**
     * @dataProvider renderDataProvider
     */
    public function testRender(ComparisonExpression $expression, string $expectedString)
    {
        $this->assertSame($expectedString, $expression->render());
    }

    public function renderDataProvider(): array
    {
        return [
            'literals, exact equals' => [
                'expression' => new ComparisonExpression(
                    new LiteralExpression('lhs'),
                    new LiteralExpression('rhs'),
                    '==='
                ),
                'expectedString' =>
                    'lhs === rhs',
            ],
            'object method invocation and literal, null coalesce' => [
                'expression' => new ComparisonExpression(
                    new ObjectMethodInvocation(
                        new VariableDependency('DEPENDENCY'),
                        'methodName'
                    ),
                    new LiteralExpression('value'),
                    '??'
                ),
                'expectedString' =>
                    '{{ DEPENDENCY }}->methodName() ?? value',
            ],
        ];
    }
}
