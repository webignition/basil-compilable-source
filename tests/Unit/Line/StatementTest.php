<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Tests\Unit\Line;

use webignition\BasilCompilableSource\Block\ClassDependencyCollection;
use webignition\BasilCompilableSource\Line\ClassDependency;
use webignition\BasilCompilableSource\Line\Expression;
use webignition\BasilCompilableSource\Line\ExpressionInterface;
use webignition\BasilCompilableSource\Line\MethodInvocation\MethodInvocation;
use webignition\BasilCompilableSource\Line\MethodInvocation\ObjectMethodInvocation;
use webignition\BasilCompilableSource\Line\Statement;
use webignition\BasilCompilableSource\Line\StatementInterface;
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
            'expression with metadata encapsulating variable dependency' => [
                'expression' => new Expression(
                    VariablePlaceholder::createDependency('DEPENDENCY'),
                    new Metadata([
                        Metadata::KEY_CLASS_DEPENDENCIES => new ClassDependencyCollection([
                            new ClassDependency(ClassDependency::class),
                        ]),
                    ])
                ),
                'expectedMetadata' => new Metadata([
                    Metadata::KEY_CLASS_DEPENDENCIES => new ClassDependencyCollection([
                        new ClassDependency(ClassDependency::class),
                    ]),
                    Metadata::KEY_VARIABLE_DEPENDENCIES => VariablePlaceholderCollection::createDependencyCollection([
                        'DEPENDENCY',
                    ])
                ]),
            ],
            'method invocation' => [
                'expression' => new MethodInvocation('methodName'),
                'expectedMetadata' => new Metadata(),
            ],
            'object method invocation' => [
                'expression' => new ObjectMethodInvocation('object', 'methodName'),
                'expectedMetadata' => new Metadata(),
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
            'statement encapsulating expression with metadata encapsulating variable dependency' => [
                'statement' => new Statement(
                    new Expression(
                        VariablePlaceholder::createDependency('DEPENDENCY'),
                        new Metadata([
                            Metadata::KEY_CLASS_DEPENDENCIES => new ClassDependencyCollection([
                                new ClassDependency(ClassDependency::class),
                            ]),
                        ])
                    )
                ),
                'expectedString' => '{{ DEPENDENCY }};',
            ],
            'statement encapsulating method invocation' => [
                'statement' => new Statement(
                    new MethodInvocation('methodName')
                ),
                'expectedString' => 'methodName();',
            ],
            'statement encapsulating object method invocation' => [
                'statement' => new Statement(
                    new ObjectMethodInvocation('object', 'methodName')
                ),
                'expectedString' => 'object->methodName();',
            ],
        ];
    }
}
