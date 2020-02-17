<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Tests\Unit\Line;

use webignition\BasilCompilableSource\Line\ExpressionInterface;
use webignition\BasilCompilableSource\Line\LiteralExpression;
use webignition\BasilCompilableSource\Line\MethodInvocation\ObjectMethodInvocation;
use webignition\BasilCompilableSource\Line\NullCoalescingExpression;
use webignition\BasilCompilableSource\Metadata\Metadata;
use webignition\BasilCompilableSource\Metadata\MetadataInterface;
use webignition\BasilCompilableSource\VariablePlaceholder;
use webignition\BasilCompilableSource\VariablePlaceholderCollection;

class NullCoalescingExpressionTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider createDataProvider
     */
    public function testCreate(
        ExpressionInterface $expression,
        ExpressionInterface $default,
        MetadataInterface $expectedMetadata
    ) {
        $nullCoalescingExpression = new NullCoalescingExpression($expression, $default);

        $this->assertEquals($expectedMetadata, $nullCoalescingExpression->getMetadata());
        $this->assertNull($nullCoalescingExpression->getCastTo());
        $this->assertSame($expression, $nullCoalescingExpression->getExpression());
        $this->assertSame($default, $nullCoalescingExpression->getDefault());
    }

    public function createDataProvider(): array
    {
        return [
            'no metadata' => [
                 new LiteralExpression('5'),
                 new LiteralExpression('6'),
                'expectedMetadata' => new Metadata(),
            ],
            'has metadata' => [
                new ObjectMethodInvocation(
                    VariablePlaceholder::createDependency('DEPENDENCY'),
                    'methodName'
                ),
                new LiteralExpression('literal'),
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
    public function testRender(NullCoalescingExpression $expression, string $expectedString)
    {
        $this->assertSame($expectedString, $expression->render());
    }

    public function renderDataProvider(): array
    {
        return [
            'literals' => [
                'expression' => new NullCoalescingExpression(
                    new LiteralExpression('expression'),
                    new LiteralExpression('default')
                ),
                'expectedString' =>
                    'expression ?? default',
            ],
            'object method invocation or literal' => [
                'expression' => new NullCoalescingExpression(
                    new ObjectMethodInvocation(
                        VariablePlaceholder::createDependency('DEPENDENCY'),
                        'methodName'
                    ),
                    new LiteralExpression('default')
                ),
                'expectedString' =>
                    '{{ DEPENDENCY }}->methodName() ?? default',
            ],

        ];
    }
}
