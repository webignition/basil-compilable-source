<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Tests\Unit\Line\Statement;

use webignition\BasilCompilableSource\Line\ObjectPropertyAccessExpression;
use webignition\BasilCompilableSource\Line\Statement\AssignmentStatement;
use webignition\BasilCompilableSource\Line\Statement\AssignmentStatementInterface;
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
    public function testCreate(VariablePlaceholder $placeholder, ExpressionInterface $expression)
    {
        $statement = new AssignmentStatement($placeholder, $expression);

        $this->assertSame($placeholder, $statement->getVariablePlaceholder());
        $this->assertSame($expression, $statement->getExpression());
    }

    public function createDataProvider(): array
    {
        return [
            'variable dependency' => [
                'placeholder' => VariablePlaceholder::createExport('PLACEHOLDER'),
                'expression' => VariablePlaceholder::createDependency('DEPENDENCY'),
            ],
            'variable export' => [
                'placeholder' => VariablePlaceholder::createExport('PLACEHOLDER'),
                'expression' => VariablePlaceholder::createExport('EXPORT'),
            ],
            'method invocation' => [
                'placeholder' => VariablePlaceholder::createExport('PLACEHOLDER'),
                'expression' => new MethodInvocation('methodName'),
            ],
            'object method invocation' => [
                'placeholder' => VariablePlaceholder::createExport('PLACEHOLDER'),
                'expression' => new ObjectMethodInvocation(
                    VariablePlaceholder::createDependency('OBJECT'),
                    'methodName'
                ),
            ],
        ];
    }

    /**
     * @dataProvider getMetadataDataProvider
     */
    public function testGetMetadata(AssignmentStatement $statement, MetadataInterface $expectedMetadata)
    {
        $this->assertEquals($expectedMetadata, $statement->getMetadata());
    }

    public function getMetadataDataProvider(): array
    {
        return [
            'expression is variable dependency' => [
                'statement' => new AssignmentStatement(
                    VariablePlaceholder::createExport('PLACEHOLDER'),
                    VariablePlaceholder::createDependency('DEPENDENCY')
                ),
                'expectedMetadata' => new Metadata([
                    Metadata::KEY_VARIABLE_DEPENDENCIES => VariablePlaceholderCollection::createDependencyCollection([
                        'DEPENDENCY',
                    ]),
                    Metadata::KEY_VARIABLE_EXPORTS => VariablePlaceholderCollection::createExportCollection([
                        'PLACEHOLDER',
                    ])
                ]),
            ],
            'variable export' => [
                'statement' => new AssignmentStatement(
                    VariablePlaceholder::createExport('PLACEHOLDER'),
                    VariablePlaceholder::createExport('EXPORT')
                ),
                'expectedMetadata' => new Metadata([
                    Metadata::KEY_VARIABLE_EXPORTS => VariablePlaceholderCollection::createExportCollection([
                        'EXPORT',
                        'PLACEHOLDER',
                    ])
                ]),
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
            'statement encapsulating variable export, export is cast to string' => [
                'statement' => new AssignmentStatement(
                    VariablePlaceholder::createExport('PLACEHOLDER'),
                    VariablePlaceholder::createExport('EXPORT', 'string')
                ),
                'expectedString' => '{{ PLACEHOLDER }} = (string) {{ EXPORT }};',
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
                    new ObjectMethodInvocation(
                        VariablePlaceholder::createDependency('OBJECT'),
                        'methodName'
                    )
                ),
                'expectedString' => '{{ PLACEHOLDER }} = {{ OBJECT }}->methodName();',
            ],
            'placeholder is object property access expression' => [
                'statement' => new AssignmentStatement(
                    new ObjectPropertyAccessExpression(
                        VariablePlaceholder::createExport('TARGET'),
                        'propertyName'
                    ),
                    new ObjectMethodInvocation(
                        VariablePlaceholder::createDependency('OBJECT'),
                        'methodName'
                    )
                ),
                'expectedString' => '{{ TARGET }}->propertyName = {{ OBJECT }}->methodName();',
            ],
        ];
    }
}
