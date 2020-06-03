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
use webignition\BasilCompilableSource\ResolvablePlaceholder;
use webignition\BasilCompilableSource\ResolvablePlaceholderCollection;

class AssignmentStatementTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider createDataProvider
     */
    public function testCreate(ResolvablePlaceholder $placeholder, ExpressionInterface $expression)
    {
        $statement = new AssignmentStatement($placeholder, $expression);

        $this->assertSame($placeholder, $statement->getVariablePlaceholder());
        $this->assertSame($expression, $statement->getExpression());
    }

    public function createDataProvider(): array
    {
        return [
            'variable dependency' => [
                'placeholder' => ResolvablePlaceholder::createExport('PLACEHOLDER'),
                'expression' => ResolvablePlaceholder::createDependency('DEPENDENCY'),
            ],
            'variable export' => [
                'placeholder' => ResolvablePlaceholder::createExport('PLACEHOLDER'),
                'expression' => ResolvablePlaceholder::createExport('EXPORT'),
            ],
            'method invocation' => [
                'placeholder' => ResolvablePlaceholder::createExport('PLACEHOLDER'),
                'expression' => new MethodInvocation('methodName'),
            ],
            'object method invocation' => [
                'placeholder' => ResolvablePlaceholder::createExport('PLACEHOLDER'),
                'expression' => new ObjectMethodInvocation(
                    ResolvablePlaceholder::createDependency('OBJECT'),
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
                    ResolvablePlaceholder::createExport('PLACEHOLDER'),
                    ResolvablePlaceholder::createDependency('DEPENDENCY')
                ),
                'expectedMetadata' => new Metadata([
                    Metadata::KEY_VARIABLE_DEPENDENCIES => ResolvablePlaceholderCollection::createDependencyCollection([
                        'DEPENDENCY',
                    ]),
                    Metadata::KEY_VARIABLE_EXPORTS => ResolvablePlaceholderCollection::createExportCollection([
                        'PLACEHOLDER',
                    ])
                ]),
            ],
            'variable export' => [
                'statement' => new AssignmentStatement(
                    ResolvablePlaceholder::createExport('PLACEHOLDER'),
                    ResolvablePlaceholder::createExport('EXPORT')
                ),
                'expectedMetadata' => new Metadata([
                    Metadata::KEY_VARIABLE_EXPORTS => ResolvablePlaceholderCollection::createExportCollection([
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
                    ResolvablePlaceholder::createExport('PLACEHOLDER'),
                    ResolvablePlaceholder::createDependency('DEPENDENCY')
                ),
                'expectedString' => '{{ PLACEHOLDER }} = {{ DEPENDENCY }};',
            ],
            'statement encapsulating variable export' => [
                'statement' => new AssignmentStatement(
                    ResolvablePlaceholder::createExport('PLACEHOLDER'),
                    ResolvablePlaceholder::createExport('EXPORT')
                ),
                'expectedString' => '{{ PLACEHOLDER }} = {{ EXPORT }};',
            ],
            'statement encapsulating variable export, export is cast to string' => [
                'statement' => new AssignmentStatement(
                    ResolvablePlaceholder::createExport('PLACEHOLDER'),
                    new CastExpression(ResolvablePlaceholder::createExport('EXPORT'), 'string')
                ),
                'expectedString' => '{{ PLACEHOLDER }} = (string) ({{ EXPORT }});',
            ],
            'statement encapsulating method invocation' => [
                'statement' => new AssignmentStatement(
                    ResolvablePlaceholder::createExport('PLACEHOLDER'),
                    new MethodInvocation('methodName')
                ),
                'expectedString' => '{{ PLACEHOLDER }} = methodName();',
            ],
            'statement encapsulating object method invocation' => [
                'statement' => new AssignmentStatement(
                    ResolvablePlaceholder::createExport('PLACEHOLDER'),
                    new ObjectMethodInvocation(
                        ResolvablePlaceholder::createDependency('OBJECT'),
                        'methodName'
                    )
                ),
                'expectedString' => '{{ PLACEHOLDER }} = {{ OBJECT }}->methodName();',
            ],
            'placeholder is object property access expression' => [
                'statement' => new AssignmentStatement(
                    new ObjectPropertyAccessExpression(
                        ResolvablePlaceholder::createExport('TARGET'),
                        'propertyName'
                    ),
                    new ObjectMethodInvocation(
                        ResolvablePlaceholder::createDependency('OBJECT'),
                        'methodName'
                    )
                ),
                'expectedString' => '{{ TARGET }}->propertyName = {{ OBJECT }}->methodName();',
            ],
        ];
    }
}
