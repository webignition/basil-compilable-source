<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Tests\Unit\Expression;

use PHPUnit\Framework\TestCase;
use webignition\BasilCompilableSource\ClassName;
use webignition\BasilCompilableSource\Expression\UseExpression;
use webignition\BasilCompilableSource\Metadata\Metadata;

class UseExpressionTest extends \PHPUnit\Framework\TestCase
{
    public function testCreate()
    {
        $expression = new UseExpression(new ClassName(UseExpressionTest::class));

        $this->assertEquals(new Metadata(), $expression->getMetadata());
    }

    /**
     * @dataProvider renderDataProvider
     */
    public function testRender(UseExpression $expression, string $expectedString)
    {
        $this->assertSame($expectedString, $expression->render());
    }

    public function renderDataProvider(): array
    {
        return [
            'no alias' => [
                'expression' => new UseExpression(new ClassName(TestCase::class)),
                'expectedString' => 'use PHPUnit\Framework\TestCase',
            ],
            'has alias' => [
                'expression' => new UseExpression(new ClassName(TestCase::class, 'AliasName')),
                'expectedString' => 'use PHPUnit\Framework\TestCase as AliasName',
            ],
        ];
    }
}
