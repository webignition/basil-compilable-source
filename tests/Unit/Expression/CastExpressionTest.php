<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Tests\Unit\Expression;

use webignition\BasilCompilableSource\Body\Body;
use webignition\BasilCompilableSource\Expression\ArrayExpression;
use webignition\BasilCompilableSource\Expression\CastExpression;
use webignition\BasilCompilableSource\Expression\ClosureExpression;
use webignition\BasilCompilableSource\Expression\ComparisonExpression;
use webignition\BasilCompilableSource\Expression\CompositeExpression;
use webignition\BasilCompilableSource\Expression\LiteralExpression;
use webignition\BasilCompilableSource\Expression\ObjectPropertyAccessExpression;
use webignition\BasilCompilableSource\MethodInvocation\MethodInvocation;
use webignition\BasilCompilableSource\MethodInvocation\ObjectMethodInvocation;
use webignition\BasilCompilableSource\MethodInvocation\StaticObjectMethodInvocation;
use webignition\BasilCompilableSource\StaticObject;
use webignition\BasilCompilableSource\Tests\Unit\AbstractResolvableTest;
use webignition\BasilCompilableSource\VariableDependency;

class CastExpressionTest extends AbstractResolvableTest
{
    public function testCreate()
    {
        $expression = new LiteralExpression('"literal"');
        $castExpression = new CastExpression($expression, 'string');

        $this->assertEquals($expression->getMetadata(), $castExpression->getMetadata());
    }

    /**
     * @dataProvider renderDataProvider
     */
    public function testRender(CastExpression $expression, string $expectedString)
    {
        $this->assertRenderResolvable($expectedString, $expression);
    }

    public function renderDataProvider(): array
    {
        return [
            'literal int as int' => [
                'expression' => new CastExpression(
                    new LiteralExpression('100'),
                    'int'
                ),
                'expectedString' => '(int) (100)',
            ],
            'literal int as string' => [
                'expression' => new CastExpression(
                    new LiteralExpression('100'),
                    'string'
                ),
                'expectedString' => '(string) (100)',
            ],
            'empty array expression as object' => [
                'expression' => new CastExpression(new ArrayExpression([]), 'object'),
                'expectedString' => '(object) ([])',
            ],
            'empty closure expression as string' => [
                'expression' => new CastExpression(new ClosureExpression(new Body([])), 'string'),
                'expectedString' =>
                    '(string) ((function () {' . "\n" .
                    "\n" .
                    '})())',
            ],
            'comparison expression as int' => [
                'expression' => new CastExpression(
                    new ComparisonExpression(
                        new LiteralExpression('"x"'),
                        new LiteralExpression('"y"'),
                        '==='
                    ),
                    'int'
                ),
                'expectedString' => '(int) ("x" === "y")',
            ],
            'composite expression as string' => [
                'expression' => new CastExpression(
                    new CompositeExpression([
                        new LiteralExpression('$_ENV'),
                        new LiteralExpression('["secret"]'),
                    ]),
                    'string'
                ),
                'expectedString' => '(string) ($_ENV["secret"])',
            ],
            'object property access expression as string' => [
                'expression' => new CastExpression(
                    new ObjectPropertyAccessExpression(
                        new VariableDependency('OBJECT'),
                        'property'
                    ),
                    'string'
                ),
                'expectedString' => '(string) ({{ OBJECT }}->property)',
            ],
            'method invocation as string' => [
                'expression' => new CastExpression(
                    new MethodInvocation('methodName'),
                    'string'
                ),
                'expectedString' => '(string) (methodName())',
            ],
            'object method invocation as string' => [
                'expression' => new CastExpression(
                    new ObjectMethodInvocation(
                        new VariableDependency('OBJECT'),
                        'methodName'
                    ),
                    'string'
                ),
                'expectedString' => '(string) ({{ OBJECT }}->methodName())',
            ],
            'static object method invocation as string, class in root namespace' => [
                'expression' => new CastExpression(
                    new StaticObjectMethodInvocation(
                        new StaticObject('Object'),
                        'methodName'
                    ),
                    'string'
                ),
                'expectedString' => '(string) (\Object::methodName())',
            ],
            'static object method invocation as string, class not in root namespace' => [
                'expression' => new CastExpression(
                    new StaticObjectMethodInvocation(
                        new StaticObject('Acme\Object'),
                        'methodName'
                    ),
                    'string'
                ),
                'expectedString' => '(string) (Object::methodName())',
            ],
        ];
    }
}
