<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Tests\Unit\Expression;

use webignition\BasilCompilableSource\Expression\ComparisonExpression;
use webignition\BasilCompilableSource\Expression\EncapsulatedExpression;
use webignition\BasilCompilableSource\Expression\LiteralExpression;
use webignition\BasilCompilableSource\Tests\Unit\AbstractResolvableTest;

class EncapsulatedExpressionTest extends AbstractResolvableTest
{
    public function testCreate()
    {
        $expression = new LiteralExpression('"literal"');
        $encapsulatedExpression = new EncapsulatedExpression($expression);

        $this->assertEquals($expression->getMetadata(), $encapsulatedExpression->getMetadata());
    }

    /**
     * @dataProvider renderDataProvider
     */
    public function testRender(EncapsulatedExpression $expression, string $expectedString)
    {
        $this->assertRenderResolvable($expectedString, $expression);
    }

    public function renderDataProvider(): array
    {
        return [
            'literal' => [
                'expression' => new EncapsulatedExpression(
                    new LiteralExpression('100')
                ),
                'expectedString' => '(100)',
            ],
            'comparison' => [
                'expression' => new EncapsulatedExpression(
                    new ComparisonExpression(
                        new LiteralExpression('$array[$index]'),
                        new LiteralExpression('null'),
                        '??'
                    )
                ),
                'expectedString' => '($array[$index] ?? null)',
            ],
        ];
    }
}
