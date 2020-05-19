<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Tests\Unit\Line;

use webignition\BasilCompilableSource\Line\ComparisonExpression;
use webignition\BasilCompilableSource\Line\EncapsulatedExpression;
use webignition\BasilCompilableSource\Line\LiteralExpression;

class EncapsulatedExpressionTest extends \PHPUnit\Framework\TestCase
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
        $this->assertSame($expectedString, $expression->render());
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
            'literal, cast to string' => [
                'expression' => new EncapsulatedExpression(
                    new LiteralExpression('100'),
                    'string'
                ),
                'expectedString' => '(string) (100)',
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
