<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Tests\Unit\Statement;

use webignition\BasilCompilableSource\Expression\ExpressionInterface;
use webignition\BasilCompilableSource\Expression\ObjectPropertyAccessExpression;
use webignition\BasilCompilableSource\Statement\ObjectPropertyAssignmentStatement;
use webignition\BasilCompilableSource\VariableDependency;

class ObjectPropertyAssignmentStatementTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider createFromExpressionDataProvider
     */
    public function testCreateFromExpression(
        ObjectPropertyAccessExpression $placeholder,
        ExpressionInterface $expression,
        ObjectPropertyAccessExpression $expectedPlaceholder
    ) {
        $statement = ObjectPropertyAssignmentStatement::createFromExpression($placeholder, $expression);

        $this->assertEquals($expectedPlaceholder, $statement->getVariableDependency());
        $this->assertSame($expression, $statement->getExpression());
    }

    public function createFromExpressionDataProvider(): array
    {
        return [
            'default' => [
                'placeholder' => new ObjectPropertyAccessExpression(
                    new VariableDependency('DEPENDENCY'),
                    'propertyName'
                ),
                'expression' => new VariableDependency('DEPENDENCY'),
                'expectedPlaceholder' => new ObjectPropertyAccessExpression(
                    new VariableDependency('DEPENDENCY'),
                    'propertyName'
                ),
            ],
        ];
    }

    /**
     * @dataProvider renderDataProvider
     */
    public function testRender(ObjectPropertyAssignmentStatement $statement, string $expectedString)
    {
        $this->assertSame($expectedString, $statement->render());
    }

    public function renderDataProvider(): array
    {
        return [
            'default' => [
                'statement' => ObjectPropertyAssignmentStatement::createFromExpression(
                    new ObjectPropertyAccessExpression(
                        new VariableDependency('PLACEHOLDER'),
                        'propertyName'
                    ),
                    new VariableDependency('DEPENDENCY')
                ),
                'expectedString' => '{{ PLACEHOLDER }}->propertyName = {{ DEPENDENCY }};',
            ],
        ];
    }
}
