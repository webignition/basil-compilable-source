<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Tests\Unit\Block\TryCatch;

use webignition\BasilCompilableSource\Block\ClassDependencyCollection;
use webignition\BasilCompilableSource\Block\TryCatch\TryBlock;
use webignition\BasilCompilableSource\Body\Body;
use webignition\BasilCompilableSource\ClassName;
use webignition\BasilCompilableSource\Expression\LiteralExpression;
use webignition\BasilCompilableSource\Metadata\Metadata;
use webignition\BasilCompilableSource\MethodInvocation\StaticObjectMethodInvocation;
use webignition\BasilCompilableSource\Statement\AssignmentStatement;
use webignition\BasilCompilableSource\Statement\Statement;
use webignition\BasilCompilableSource\StaticObject;
use webignition\BasilCompilableSource\VariableDependency;
use webignition\BasilCompilableSource\VariableDependencyCollection;

class TryBlockTest extends \PHPUnit\Framework\TestCase
{
    public function testGetMetadata()
    {
        $body = new Body([
            new AssignmentStatement(
                new VariableDependency('DEPENDENCY'),
                new StaticObjectMethodInvocation(
                    new StaticObject(\RuntimeException::class),
                    'staticMethodName'
                )
            ),
        ]);

        $tryBlock = new TryBlock($body);

        $expectedMetadata = new Metadata([
            Metadata::KEY_CLASS_DEPENDENCIES => new ClassDependencyCollection([
                new ClassName(\RuntimeException::class),
            ]),
            Metadata::KEY_VARIABLE_DEPENDENCIES => new VariableDependencyCollection([
                'DEPENDENCY',
            ]),
        ]);

        $this->assertEquals($expectedMetadata, $tryBlock->getMetadata());
    }

    /**
     * @dataProvider renderDataProvider
     */
    public function testRender(TryBlock $tryBlock, string $expectedString)
    {
        $this->assertSame($expectedString, $tryBlock->render());
    }

    public function renderDataProvider(): array
    {
        return [
            'default' => [
                'tryBlock' => new TryBlock(
                    new Statement(
                        new LiteralExpression('"literal expression"')
                    )
                ),
                'expectedString' =>
                    'try {' . "\n" .
                    '    "literal expression";' . "\n" .
                    '}',
            ],
        ];
    }
}
