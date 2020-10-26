<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Tests\Unit\Expression;

use webignition\BasilCompilableSource\Expression\LiteralExpression;
use webignition\BasilCompilableSource\Metadata\Metadata;
use webignition\BasilCompilableSource\Tests\Unit\AbstractResolvableTest;

class LiteralExpressionTest extends AbstractResolvableTest
{
    /**
     * @dataProvider createDataProvider
     */
    public function testCreate(string $content)
    {
        $expression = new LiteralExpression($content);

        $this->assertEquals(new Metadata(), $expression->getMetadata());
    }

    public function createDataProvider(): array
    {
        return [
            'empty' => [
                'content' => '',
            ],
            'string' => [
                'content' => '"value"',
            ],
            'int' => [
                'content' => '1',
            ],
        ];
    }

    /**
     * @dataProvider renderDataProvider
     */
    public function testRender(LiteralExpression $expression, string $expectedString)
    {
        $this->assertRenderResolvable($expectedString, $expression);
    }

    public function renderDataProvider(): array
    {
        return [
            'empty' => [
                'expression' => new LiteralExpression(''),
                'expectedString' => '',
            ],
            'string' => [
                'expression' => new LiteralExpression('"value"'),
                'expectedString' => '"value"',
            ],
            'int' => [
                'expression' => new LiteralExpression('2'),
                'expectedString' => '2',
            ],
        ];
    }
}
