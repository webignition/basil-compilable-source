<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Tests\Unit\Statement;

use webignition\BasilCompilableSource\Expression\CastExpression;
use webignition\BasilCompilableSource\Expression\ExpressionInterface;
use webignition\BasilCompilableSource\Expression\ObjectPropertyAccessExpression;
use webignition\BasilCompilableSource\Metadata\Metadata;
use webignition\BasilCompilableSource\Metadata\MetadataInterface;
use webignition\BasilCompilableSource\MethodInvocation\MethodInvocation;
use webignition\BasilCompilableSource\MethodInvocation\ObjectMethodInvocation;
use webignition\BasilCompilableSource\Statement\AssignmentStatement;
use webignition\BasilCompilableSource\Statement\AssignmentStatementInterface;
use webignition\BasilCompilableSource\VariableDependency;
use webignition\BasilCompilableSource\VariableDependencyCollection;
use webignition\BasilCompilableSource\VariableName;

class AssignmentStatementTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider createFromExpressionDataProvider
     */
    public function testCreateFromExpression(VariableDependency $placeholder, ExpressionInterface $expression)
    {
        $statement = AssignmentStatement::createFromExpression($placeholder, $expression);

        $this->assertSame($placeholder, $statement->getVariableDependency());
        $this->assertSame($expression, $statement->getExpression());
    }

    public function createFromExpressionDataProvider(): array
    {
        return [
            'variable dependency' => [
                'placeholder' => new VariableDependency('PLACEHOLDER'),
                'expression' => new VariableDependency('DEPENDENCY'),
            ],
            'method invocation' => [
                'placeholder' => new VariableDependency('PLACEHOLDER'),
                'expression' => new MethodInvocation('methodName'),
            ],
            'object method invocation' => [
                'placeholder' => new VariableDependency('PLACEHOLDER'),
                'expression' => new ObjectMethodInvocation(
                    new VariableDependency('OBJECT'),
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
                'statement' => AssignmentStatement::createFromExpression(
                    new VariableDependency('PLACEHOLDER'),
                    new VariableDependency('DEPENDENCY')
                ),
                'expectedMetadata' => new Metadata([
                    Metadata::KEY_VARIABLE_DEPENDENCIES => new VariableDependencyCollection([
                        'PLACEHOLDER',
                        'DEPENDENCY',
                    ]),
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
                'statement' => AssignmentStatement::createFromExpression(
                    new VariableDependency('PLACEHOLDER'),
                    new VariableDependency('DEPENDENCY')
                ),
                'expectedString' => '{{ PLACEHOLDER }} = {{ DEPENDENCY }};',
            ],
            'statement encapsulating variable, variable is cast to string' => [
                'statement' => AssignmentStatement::createFromExpression(
                    new VariableDependency('PLACEHOLDER'),
                    new CastExpression(new VariableName('variable'), 'string')
                ),
                'expectedString' => '{{ PLACEHOLDER }} = (string) ($variable);',
            ],
            'statement encapsulating method invocation' => [
                'statement' => AssignmentStatement::createFromExpression(
                    new VariableDependency('PLACEHOLDER'),
                    new MethodInvocation('methodName')
                ),
                'expectedString' => '{{ PLACEHOLDER }} = methodName();',
            ],
            'statement encapsulating object method invocation' => [
                'statement' => AssignmentStatement::createFromExpression(
                    new VariableDependency('PLACEHOLDER'),
                    new ObjectMethodInvocation(
                        new VariableDependency('OBJECT'),
                        'methodName'
                    )
                ),
                'expectedString' => '{{ PLACEHOLDER }} = {{ OBJECT }}->methodName();',
            ],
            'placeholder is object property access expression' => [
                'statement' => AssignmentStatement::createFromExpression(
                    new ObjectPropertyAccessExpression(
                        new VariableDependency('TARGET'),
                        'propertyName'
                    ),
                    new ObjectMethodInvocation(
                        new VariableDependency('OBJECT'),
                        'methodName'
                    )
                ),
                'expectedString' => '{{ TARGET }}->propertyName = {{ OBJECT }}->methodName();',
            ],
        ];
    }
}
