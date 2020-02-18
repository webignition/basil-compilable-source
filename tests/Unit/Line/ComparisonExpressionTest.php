<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Tests\Unit\Line;

use webignition\BasilCompilableSource\Line\ComparisonExpression;
use webignition\BasilCompilableSource\Line\ExpressionInterface;
use webignition\BasilCompilableSource\Line\LiteralExpression;
use webignition\BasilCompilableSource\Line\MethodInvocation\ObjectMethodInvocation;
use webignition\BasilCompilableSource\Metadata\Metadata;
use webignition\BasilCompilableSource\Metadata\MetadataInterface;
use webignition\BasilCompilableSource\VariablePlaceholder;
use webignition\BasilCompilableSource\VariablePlaceholderCollection;

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
        $this->assertNull($expression->getCastTo());
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
                    VariablePlaceholder::createDependency('DEPENDENCY'),
                    'methodName'
                ),
                'rightHandSide' => new LiteralExpression('literal'),
                'comparison' => '!==',
                'expectedMetadata' => new Metadata([
                    Metadata::KEY_VARIABLE_DEPENDENCIES => VariablePlaceholderCollection::createDependencyCollection([
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
                        VariablePlaceholder::createDependency('DEPENDENCY'),
                        'methodName'
                    ),
                    new LiteralExpression('value'),
                    '??'
                ),
                'expectedString' =>
                    '{{ DEPENDENCY }}->methodName() ?? value',
            ],
            'literals, exact equals, cast to string' => [
                'expression' => new ComparisonExpression(
                    new LiteralExpression('lhs'),
                    new LiteralExpression('rhs'),
                    '===',
                    'string'
                ),
                'expectedString' =>
                    '(string) (lhs === rhs)',
            ],
        ];
    }
}
