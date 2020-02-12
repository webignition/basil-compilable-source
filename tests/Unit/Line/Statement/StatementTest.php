<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Tests\Unit\Line\Statement;

use webignition\BasilCompilableSource\Block\ClassDependencyCollection;
use webignition\BasilCompilableSource\Line\ClassDependency;
use webignition\BasilCompilableSource\Line\ExpressionInterface;
use webignition\BasilCompilableSource\Line\MethodInvocation\MethodInvocation;
use webignition\BasilCompilableSource\Line\MethodInvocation\ObjectMethodInvocation;
use webignition\BasilCompilableSource\Line\Statement\Statement;
use webignition\BasilCompilableSource\Line\Statement\StatementInterface;
use webignition\BasilCompilableSource\Metadata\Metadata;
use webignition\BasilCompilableSource\Metadata\MetadataInterface;
use webignition\BasilCompilableSource\VariablePlaceholder;
use webignition\BasilCompilableSource\VariablePlaceholderCollection;

class StatementTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider createDataProvider
     */
    public function testCreate(ExpressionInterface $expression, MetadataInterface $expectedMetadata)
    {
        $statement = new Statement($expression);

        $this->assertEquals($expectedMetadata, $statement->getMetadata());
        $this->assertSame($expression, $statement->getExpression());
    }

    public function createDataProvider(): array
    {
        return [
            'variable dependency' => [
                'expression' => VariablePlaceholder::createDependency('DEPENDENCY'),
                'expectedMetadata' => new Metadata([
                    Metadata::KEY_VARIABLE_DEPENDENCIES => VariablePlaceholderCollection::createDependencyCollection([
                        'DEPENDENCY',
                    ])
                ]),
            ],
            'variable export' => [
                'expression' => VariablePlaceholder::createExport('EXPORT'),
                'expectedMetadata' => new Metadata([
                    Metadata::KEY_VARIABLE_EXPORTS => VariablePlaceholderCollection::createExportCollection([
                        'EXPORT',
                    ])
                ]),
            ],
            'method invocation' => [
                'expression' => new MethodInvocation('methodName'),
                'expectedMetadata' => new Metadata(),
            ],
            'object method invocation' => [
                'expression' => new ObjectMethodInvocation(
                    VariablePlaceholder::createDependency('OBJECT'),
                    'methodName'
                ),
                'expectedMetadata' => new Metadata([
                    Metadata::KEY_VARIABLE_DEPENDENCIES => VariablePlaceholderCollection::createDependencyCollection([
                        'OBJECT',
                    ])
                ]),
            ],
        ];
    }

    /**
     * @dataProvider renderDataProvider
     */
    public function testRender(StatementInterface $statement, string $expectedString)
    {
        $this->assertSame($expectedString, $statement->render());
    }

    public function renderDataProvider(): array
    {
        return [
            'statement encapsulating variable dependency' => [
                'statement' => new Statement(
                    VariablePlaceholder::createDependency('DEPENDENCY')
                ),
                'expectedString' => '{{ DEPENDENCY }};',
            ],
            'statement encapsulating variable export' => [
                'statement' => new Statement(
                    VariablePlaceholder::createExport('EXPORT')
                ),
                'expectedString' => '{{ EXPORT }};',
            ],
            'statement encapsulating method invocation' => [
                'statement' => new Statement(
                    new MethodInvocation('methodName')
                ),
                'expectedString' => 'methodName();',
            ],
            'statement encapsulating object method invocation' => [
                'statement' => new Statement(
                    new ObjectMethodInvocation(
                        VariablePlaceholder::createDependency('OBJECT'),
                        'methodName'
                    )
                ),
                'expectedString' => '{{ OBJECT }}->methodName();',
            ],
        ];
    }
}
