<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Tests\Unit\Expression;

use webignition\BasilCompilableSource\Expression\AssignmentExpression;
use webignition\BasilCompilableSource\Expression\ExpressionInterface;
use webignition\BasilCompilableSource\Expression\LiteralExpression;
use webignition\BasilCompilableSource\Expression\ObjectPropertyAccessExpression;
use webignition\BasilCompilableSource\Metadata\Metadata;
use webignition\BasilCompilableSource\Metadata\MetadataInterface;
use webignition\BasilCompilableSource\MethodInvocation\ObjectMethodInvocation;
use webignition\BasilCompilableSource\Tests\Unit\AbstractResolvableTest;
use webignition\BasilCompilableSource\VariableDependency;
use webignition\BasilCompilableSource\VariableDependencyCollection;

class AssignmentExpressionTest extends AbstractResolvableTest
{
    /**
     * @dataProvider createDataProvider
     */
    public function testCreate(
        ExpressionInterface $variable,
        ExpressionInterface $value,
        string $operator,
        MetadataInterface $expectedMetadata
    ): void {
        $expression = new AssignmentExpression($variable, $value, $operator);

        $this->assertEquals($expectedMetadata, $expression->getMetadata());
        $this->assertSame($variable, $expression->getVariable());
        $this->assertSame($value, $expression->getValue());
        $this->assertSame($operator, $expression->getOperator());
    }

    /**
     * @return array[]
     */
    public function createDataProvider(): array
    {
        return [
            'no metadata' => [
                'variable' => new LiteralExpression('5'),
                'value' => new LiteralExpression('6'),
                'operator' => '===',
                'expectedMetadata' => new Metadata(),
            ],
            'has metadata' => [
                'variable' => new ObjectMethodInvocation(
                    new VariableDependency('DEPENDENCY'),
                    'methodName'
                ),
                'value' => new LiteralExpression('literal'),
                'operator' => '!==',
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
    public function testRender(AssignmentExpression $expression, string $expectedString): void
    {
        $this->assertRenderResolvable($expectedString, $expression);
    }

    /**
     * @return array[]
     */
    public function renderDataProvider(): array
    {
        return [
            'literals, assignment' => [
                'expression' => new AssignmentExpression(
                    new LiteralExpression('lhs'),
                    new LiteralExpression('rhs')
                ),
                'expectedString' =>
                    'lhs = rhs',
            ],
            'object property access and literal, assignment' => [
                'expression' => new AssignmentExpression(
                    new ObjectPropertyAccessExpression(
                        new VariableDependency('DEPENDENCY'),
                        'propertyName'
                    ),
                    new LiteralExpression('value')
                ),
                'expectedString' =>
                    '{{ DEPENDENCY }}->propertyName = value',
            ],
        ];
    }
}
