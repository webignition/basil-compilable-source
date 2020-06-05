<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Tests\Unit\Line\Statement;

use webignition\BasilCompilableSource\Line\CastExpression;
use webignition\BasilCompilableSource\Line\ObjectPropertyAccessExpression;
use webignition\BasilCompilableSource\Line\Statement\AssignmentStatement;
use webignition\BasilCompilableSource\Line\Statement\AssignmentStatementInterface;
use webignition\BasilCompilableSource\Line\ExpressionInterface;
use webignition\BasilCompilableSource\Line\MethodInvocation\MethodInvocation;
use webignition\BasilCompilableSource\Line\MethodInvocation\ObjectMethodInvocation;
use webignition\BasilCompilableSource\Metadata\Metadata;
use webignition\BasilCompilableSource\Metadata\MetadataInterface;
use webignition\BasilCompilableSource\VariableDependency;
use webignition\BasilCompilableSource\VariableDependencyCollection;

class AssignmentStatementTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider createDataProvider
     */
    public function testCreate(VariableDependency $placeholder, ExpressionInterface $expression)
    {
        $statement = new AssignmentStatement($placeholder, $expression);

        $this->assertSame($placeholder, $statement->getVariableDependency());
        $this->assertSame($expression, $statement->getExpression());
    }

    public function createDataProvider(): array
    {
        return [
            'variable dependency' => [
                'placeholder' => VariableDependency::createExport('PLACEHOLDER'),
                'expression' => VariableDependency::createDependency('DEPENDENCY'),
            ],
            'variable export' => [
                'placeholder' => VariableDependency::createExport('PLACEHOLDER'),
                'expression' => VariableDependency::createExport('EXPORT'),
            ],
            'method invocation' => [
                'placeholder' => VariableDependency::createExport('PLACEHOLDER'),
                'expression' => new MethodInvocation('methodName'),
            ],
            'object method invocation' => [
                'placeholder' => VariableDependency::createExport('PLACEHOLDER'),
                'expression' => new ObjectMethodInvocation(
                    VariableDependency::createDependency('OBJECT'),
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
                    VariableDependency::createExport('PLACEHOLDER'),
                    VariableDependency::createDependency('DEPENDENCY')
                ),
                'expectedMetadata' => new Metadata([
                    Metadata::KEY_VARIABLE_DEPENDENCIES => VariableDependencyCollection::createDependencyCollection([
                        'DEPENDENCY',
                    ]),
                    Metadata::KEY_VARIABLE_EXPORTS => VariableDependencyCollection::createExportCollection([
                        'PLACEHOLDER',
                    ])
                ]),
            ],
            'variable export' => [
                'statement' => new AssignmentStatement(
                    VariableDependency::createExport('PLACEHOLDER'),
                    VariableDependency::createExport('EXPORT')
                ),
                'expectedMetadata' => new Metadata([
                    Metadata::KEY_VARIABLE_EXPORTS => VariableDependencyCollection::createExportCollection([
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
                    VariableDependency::createExport('PLACEHOLDER'),
                    VariableDependency::createDependency('DEPENDENCY')
                ),
                'expectedString' => '{{ PLACEHOLDER }} = {{ DEPENDENCY }};',
            ],
            'statement encapsulating variable export' => [
                'statement' => new AssignmentStatement(
                    VariableDependency::createExport('PLACEHOLDER'),
                    VariableDependency::createExport('EXPORT')
                ),
                'expectedString' => '{{ PLACEHOLDER }} = {{ EXPORT }};',
            ],
            'statement encapsulating variable export, export is cast to string' => [
                'statement' => new AssignmentStatement(
                    VariableDependency::createExport('PLACEHOLDER'),
                    new CastExpression(VariableDependency::createExport('EXPORT'), 'string')
                ),
                'expectedString' => '{{ PLACEHOLDER }} = (string) ({{ EXPORT }});',
            ],
            'statement encapsulating method invocation' => [
                'statement' => new AssignmentStatement(
                    VariableDependency::createExport('PLACEHOLDER'),
                    new MethodInvocation('methodName')
                ),
                'expectedString' => '{{ PLACEHOLDER }} = methodName();',
            ],
            'statement encapsulating object method invocation' => [
                'statement' => new AssignmentStatement(
                    VariableDependency::createExport('PLACEHOLDER'),
                    new ObjectMethodInvocation(
                        VariableDependency::createDependency('OBJECT'),
                        'methodName'
                    )
                ),
                'expectedString' => '{{ PLACEHOLDER }} = {{ OBJECT }}->methodName();',
            ],
            'placeholder is object property access expression' => [
                'statement' => new AssignmentStatement(
                    new ObjectPropertyAccessExpression(
                        VariableDependency::createExport('TARGET'),
                        'propertyName'
                    ),
                    new ObjectMethodInvocation(
                        VariableDependency::createDependency('OBJECT'),
                        'methodName'
                    )
                ),
                'expectedString' => '{{ TARGET }}->propertyName = {{ OBJECT }}->methodName();',
            ],
        ];
    }
}
