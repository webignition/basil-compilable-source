<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Tests\Unit\Block\IfBlock;

use webignition\BasilCompilableSource\Block\ClassDependencyCollection;
use webignition\BasilCompilableSource\Block\IfBlock\IfBlock;
use webignition\BasilCompilableSource\Body\Body;
use webignition\BasilCompilableSource\ClassName;
use webignition\BasilCompilableSource\Expression\AssignmentExpression;
use webignition\BasilCompilableSource\Expression\ComparisonExpression;
use webignition\BasilCompilableSource\Expression\LiteralExpression;
use webignition\BasilCompilableSource\Expression\ReturnExpression;
use webignition\BasilCompilableSource\Metadata\Metadata;
use webignition\BasilCompilableSource\MethodInvocation\ObjectMethodInvocation;
use webignition\BasilCompilableSource\MethodInvocation\StaticObjectMethodInvocation;
use webignition\BasilCompilableSource\Statement\Statement;
use webignition\BasilCompilableSource\StaticObject;
use webignition\BasilCompilableSource\Tests\Unit\AbstractResolvableTest;
use webignition\BasilCompilableSource\VariableDependency;
use webignition\BasilCompilableSource\VariableDependencyCollection;

class IfBlockTest extends AbstractResolvableTest
{
    public function testGetMetadata(): void
    {
        $expression = new ComparisonExpression(
            new ObjectMethodInvocation(
                new VariableDependency('IF_EXPRESSION_OBJECT'),
                'methodName'
            ),
            new LiteralExpression('value'),
            '==='
        );

        $body = new Body([
            new Statement(
                new AssignmentExpression(
                    new VariableDependency('BODY_DEPENDENCY'),
                    new StaticObjectMethodInvocation(
                        new StaticObject(\RuntimeException::class),
                        'staticMethodName'
                    )
                )
            ),
        ]);

        $ifBlock = new IfBlock($expression, $body);

        $expectedMetadata = new Metadata([
            Metadata::KEY_CLASS_DEPENDENCIES => new ClassDependencyCollection([
                new ClassName(\RuntimeException::class),
            ]),
            Metadata::KEY_VARIABLE_DEPENDENCIES => new VariableDependencyCollection([
                'BODY_DEPENDENCY',
                'IF_EXPRESSION_OBJECT',
            ]),
        ]);

        $this->assertEquals($expectedMetadata, $ifBlock->getMetadata());
    }

    /**
     * @dataProvider renderDataProvider
     */
    public function testRender(IfBlock $ifBlock, string $expectedString): void
    {
        $this->assertRenderResolvable($expectedString, $ifBlock);
    }

    /**
     * @return array<mixed>
     */
    public function renderDataProvider(): array
    {
        return [
            'default' => [
                'ifBlock' => new IfBlock(
                    new ComparisonExpression(
                        new LiteralExpression('"value"'),
                        new LiteralExpression('"another value"'),
                        '!=='
                    ),
                    new Statement(
                        new ReturnExpression(
                            new LiteralExpression('"return value"')
                        )
                    )
                ),
                'expectedString' => 'if ("value" !== "another value") {' . "\n" .
                    '    return "return value";' . "\n" .
                    '}',
            ],
        ];
    }
}
