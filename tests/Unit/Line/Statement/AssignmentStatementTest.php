<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Tests\Unit\Line\Statement;

use webignition\BasilCompilableSource\Block\ClassDependencyCollection;
use webignition\BasilCompilableSource\Line\Statement\AssignmentStatement;
use webignition\BasilCompilableSource\Line\Statement\AssignmentStatementInterface;
use webignition\BasilCompilableSource\Line\ClassDependency;
use webignition\BasilCompilableSource\Line\Expression;
use webignition\BasilCompilableSource\Line\ExpressionInterface;
use webignition\BasilCompilableSource\Line\MethodInvocation\MethodInvocation;
use webignition\BasilCompilableSource\Line\MethodInvocation\ObjectMethodInvocation;
use webignition\BasilCompilableSource\Metadata\Metadata;
use webignition\BasilCompilableSource\Metadata\MetadataInterface;
use webignition\BasilCompilableSource\VariablePlaceholder;
use webignition\BasilCompilableSource\VariablePlaceholderCollection;

class AssignmentStatementTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider createDataProvider
     */
    public function testCreate(
        VariablePlaceholder $placeholder,
        ExpressionInterface $expression,
        MetadataInterface $expectedMetadata
    ) {
        $statement = new AssignmentStatement($placeholder, $expression);

        $this->assertSame($placeholder, $statement->getVariablePlaceholder());
        $this->assertEquals($expectedMetadata, $statement->getMetadata());
        $this->assertSame($expression, $statement->getExpression());
    }

    public function createDataProvider(): array
    {
        return [
            'variable dependency' => [
                'placeholder' => VariablePlaceholder::createExport('PLACEHOLDER'),
                'expression' => VariablePlaceholder::createDependency('DEPENDENCY'),
                'expectedMetadata' => new Metadata([
                    Metadata::KEY_VARIABLE_DEPENDENCIES => VariablePlaceholderCollection::createDependencyCollection([
                        'DEPENDENCY',
                    ])
                ]),
            ],
            'variable export' => [
                'placeholder' => VariablePlaceholder::createExport('PLACEHOLDER'),
                'expression' => VariablePlaceholder::createExport('EXPORT'),
                'expectedMetadata' => new Metadata([
                    Metadata::KEY_VARIABLE_EXPORTS => VariablePlaceholderCollection::createExportCollection([
                        'EXPORT',
                    ])
                ]),
            ],
            'expression with metadata encapsulating variable dependency' => [
                'placeholder' => VariablePlaceholder::createExport('PLACEHOLDER'),
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
                'placeholder' => VariablePlaceholder::createExport('PLACEHOLDER'),
                'expression' => new MethodInvocation('methodName'),
                'expectedMetadata' => new Metadata(),
            ],
            'object method invocation' => [
                'placeholder' => VariablePlaceholder::createExport('PLACEHOLDER'),
                'expression' => new ObjectMethodInvocation('object', 'methodName'),
                'expectedMetadata' => new Metadata(),
            ],
        ];
    }

    /**
     * @dataProvider renderDataProvider
     */
    public function testRender(AssignmentStatementInterface $statement, string $expectedString)
    {
        $this->assertSame($expectedString, $statement->render());
    }

    public function renderDataProvider(): array
    {
        return [
            'statement encapsulating variable dependency' => [
                'statement' => new AssignmentStatement(
                    VariablePlaceholder::createExport('PLACEHOLDER'),
                    VariablePlaceholder::createDependency('DEPENDENCY')
                ),
                'expectedString' => '{{ PLACEHOLDER }} = {{ DEPENDENCY }};',
            ],
            'statement encapsulating variable export' => [
                'statement' => new AssignmentStatement(
                    VariablePlaceholder::createExport('PLACEHOLDER'),
                    VariablePlaceholder::createExport('EXPORT')
                ),
                'expectedString' => '{{ PLACEHOLDER }} = {{ EXPORT }};',
            ],
            'statement encapsulating expression with metadata encapsulating variable dependency' => [
                'statement' => new AssignmentStatement(
                    VariablePlaceholder::createExport('PLACEHOLDER'),
                    new Expression(
                        VariablePlaceholder::createDependency('DEPENDENCY'),
                        new Metadata([
                            Metadata::KEY_CLASS_DEPENDENCIES => new ClassDependencyCollection([
                                new ClassDependency(ClassDependency::class),
                            ]),
                        ])
                    )
                ),
                'expectedString' => '{{ PLACEHOLDER }} = {{ DEPENDENCY }};',
            ],
            'statement encapsulating method invocation' => [
                'statement' => new AssignmentStatement(
                    VariablePlaceholder::createExport('PLACEHOLDER'),
                    new MethodInvocation('methodName')
                ),
                'expectedString' => '{{ PLACEHOLDER }} = methodName();',
            ],
            'statement encapsulating object method invocation' => [
                'statement' => new AssignmentStatement(
                    VariablePlaceholder::createExport('PLACEHOLDER'),
                    new ObjectMethodInvocation('object', 'methodName')
                ),
                'expectedString' => '{{ PLACEHOLDER }} = object->methodName();',
            ],
        ];
    }
}
