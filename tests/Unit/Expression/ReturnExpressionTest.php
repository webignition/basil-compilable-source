<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Tests\Unit\Expression;

use webignition\BasilCompilableSource\Expression\LiteralExpression;
use webignition\BasilCompilableSource\Expression\ReturnExpression;
use webignition\BasilCompilableSource\Tests\Unit\AbstractResolvableTest;

class ReturnExpressionTest extends AbstractResolvableTest
{
    /**
     * @dataProvider renderDataProvider
     */
    public function testRender(ReturnExpression $expression, string $expectedString): void
    {
        $this->assertRenderResolvable($expectedString, $expression);
    }

    /**
     * @return array<mixed>
     */
    public function renderDataProvider(): array
    {
        return [
            'empty return' => [
                'expression' => new ReturnExpression(),
                'expectedString' => 'return',
            ],
            'return an expression' => [
                'expression' => new ReturnExpression(
                    new LiteralExpression('100')
                ),
                'expectedString' => 'return 100',
            ],
        ];
    }
}
