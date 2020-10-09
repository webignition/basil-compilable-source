<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Tests\Unit\MethodInvocation;

use webignition\BasilCompilableSource\MethodInvocation\ErrorSuppressedMethodInvocation;
use webignition\BasilCompilableSource\MethodInvocation\MethodInvocation;
use webignition\BasilCompilableSource\MethodInvocation\ObjectMethodInvocation;
use webignition\BasilCompilableSource\MethodInvocation\StaticObjectMethodInvocation;
use webignition\BasilCompilableSource\StaticObject;
use webignition\BasilCompilableSource\VariableDependency;

class ErrorSuppressedMethodInvocationTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider renderDataProvider
     */
    public function testRender(ErrorSuppressedMethodInvocation $invocation, string $expectedString)
    {
        self::assertSame($expectedString, $invocation->render());
    }

    public function renderDataProvider(): array
    {
        return [
            'MethodInvocation' => [
                'invocation' => new ErrorSuppressedMethodInvocation(
                    new MethodInvocation('methodName')
                ),
                'expectedString' => '@methodName()',
            ],
            'ObjectMethodInvocation' => [
                'invocation' => new ErrorSuppressedMethodInvocation(
                    new ObjectMethodInvocation(
                        new VariableDependency('OBJECT'),
                        'methodName'
                    )
                ),
                'expectedString' => '@{{ OBJECT }}->methodName()',
            ],
            'StaticObjectMethodInvocation' => [
                'invocation' => new ErrorSuppressedMethodInvocation(
                    new StaticObjectMethodInvocation(
                        new StaticObject(
                            'parent'
                        ),
                        'methodName'
                    )
                ),
                'expectedString' => '@parent::methodName()',
            ],
        ];
    }
}
