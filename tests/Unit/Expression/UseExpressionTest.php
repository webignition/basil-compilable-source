<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Tests\Unit\Expression;

use PHPUnit\Framework\TestCase;
use webignition\BasilCompilableSource\ClassName;
use webignition\BasilCompilableSource\Expression\UseExpression;
use webignition\BasilCompilableSource\Metadata\Metadata;
use webignition\BasilCompilableSource\Tests\Unit\AbstractResolvableTest;

class UseExpressionTest extends AbstractResolvableTest
{
    public function testCreate(): void
    {
        $expression = new UseExpression(new ClassName(UseExpressionTest::class));

        $this->assertEquals(new Metadata(), $expression->getMetadata());
    }

    /**
     * @dataProvider renderDataProvider
     */
    public function testRender(UseExpression $expression, string $expectedString): void
    {
        $this->assertRenderResolvable($expectedString, $expression);
    }

    /**
     * @return array<mixed>
     */
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
