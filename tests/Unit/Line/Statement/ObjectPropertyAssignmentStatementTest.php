<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Tests\Unit\Line\Statement;

use webignition\BasilCompilableSource\Line\ObjectPropertyAccessExpression;
use webignition\BasilCompilableSource\Line\Statement\AssignmentStatementInterface;
use webignition\BasilCompilableSource\Line\ExpressionInterface;
use webignition\BasilCompilableSource\Line\Statement\ObjectPropertyAssignmentStatement;
use webignition\BasilCompilableSource\ResolvablePlaceholder;

class ObjectPropertyAssignmentStatementTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider createDataProvider
     */
    public function testCreate(
        ObjectPropertyAccessExpression $placeholder,
        ExpressionInterface $expression,
        ObjectPropertyAccessExpression $expectedPlaceholder
    ) {
        $statement = new ObjectPropertyAssignmentStatement($placeholder, $expression);

        $this->assertEquals($expectedPlaceholder, $statement->getVariablePlaceholder());
        $this->assertSame($expression, $statement->getExpression());
    }

    public function createDataProvider(): array
    {
        return [
            'default' => [
                'placeholder' => new ObjectPropertyAccessExpression(
                    ResolvablePlaceholder::createExport('DEPENDENCY'),
                    'propertyName'
                ),
                'expression' => ResolvablePlaceholder::createDependency('DEPENDENCY'),
                'expectedPlaceholder' => new ObjectPropertyAccessExpression(
                    ResolvablePlaceholder::createExport('DEPENDENCY'),
                    'propertyName'
                ),
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
            'default' => [
                'statement' => new ObjectPropertyAssignmentStatement(
                    new ObjectPropertyAccessExpression(
                        ResolvablePlaceholder::createExport('PLACEHOLDER'),
                        'propertyName'
                    ),
                    ResolvablePlaceholder::createDependency('DEPENDENCY')
                ),
                'expectedString' => '{{ PLACEHOLDER }}->propertyName = {{ DEPENDENCY }};',
            ],
        ];
    }
}
