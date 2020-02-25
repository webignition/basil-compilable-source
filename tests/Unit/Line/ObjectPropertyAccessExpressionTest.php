<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Tests\Unit\Line;

use webignition\BasilCompilableSource\Line\ObjectPropertyAccessExpression;
use webignition\BasilCompilableSource\Metadata\Metadata;
use webignition\BasilCompilableSource\Metadata\MetadataInterface;
use webignition\BasilCompilableSource\VariablePlaceholder;
use webignition\BasilCompilableSource\VariablePlaceholderCollection;

class ObjectPropertyAccessExpressionTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider createDataProvider
     */
    public function testCreate(
        VariablePlaceholder $objectPlaceholder,
        string $property,
        ?string $castTo,
        MetadataInterface $expectedMetadata
    ) {
        $invocation = new ObjectPropertyAccessExpression($objectPlaceholder, $property, $castTo);

        $this->assertSame($objectPlaceholder, $invocation->getObjectPlaceholder());
        $this->assertSame($property, $invocation->getProperty());
        $this->assertSame($castTo, $invocation->getCastTo());
        $this->assertEquals($expectedMetadata, $invocation->getMetadata());
    }

    public function createDataProvider(): array
    {
        return [
            'no castTo' => [
                'objectPlaceholder' => VariablePlaceholder::createDependency('OBJECT'),
                'property' => 'propertyName',
                'castTo' => null,
                'expectedMetadata' => new Metadata([
                    Metadata::KEY_VARIABLE_DEPENDENCIES => VariablePlaceholderCollection::createDependencyCollection([
                        'OBJECT',
                    ]),
                ]),
            ],
            'has castTo' => [
                'objectPlaceholder' => VariablePlaceholder::createDependency('OBJECT'),
                'property' => 'propertyName',
                'castTo' => 'string',
                'expectedMetadata' => new Metadata([
                    Metadata::KEY_VARIABLE_DEPENDENCIES => VariablePlaceholderCollection::createDependencyCollection([
                        'OBJECT',
                    ]),
                ]),
            ],
        ];
    }

    /**
     * @dataProvider renderDataProvider
     */
    public function testRender(ObjectPropertyAccessExpression $expression, string $expectedString)
    {
        $this->assertSame($expectedString, $expression->render());
    }

    public function renderDataProvider(): array
    {
        return [
            'no castTo' => [
                'expression' => new ObjectPropertyAccessExpression(
                    VariablePlaceholder::createDependency('OBJECT'),
                    'propertyName'
                ),
                'expectedString' => '{{ OBJECT }}->propertyName',
            ],
            'has castTo' => [
                'expression' => new ObjectPropertyAccessExpression(
                    VariablePlaceholder::createDependency('OBJECT'),
                    'propertyName',
                    'string'
                ),
                'expectedString' => '(string) {{ OBJECT }}->propertyName',
            ],
        ];
    }
}
