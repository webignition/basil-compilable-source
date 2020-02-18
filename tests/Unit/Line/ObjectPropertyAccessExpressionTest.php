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
        MetadataInterface $expectedMetadata
    ) {
        $invocation = new ObjectPropertyAccessExpression($objectPlaceholder, $property);

        $this->assertSame($objectPlaceholder, $invocation->getObjectPlaceholder());
        $this->assertSame($property, $invocation->getProperty());
        $this->assertEquals($expectedMetadata, $invocation->getMetadata());
    }

    public function createDataProvider(): array
    {
        return [
            'default' => [
                'objectPlaceholder' => VariablePlaceholder::createDependency('OBJECT'),
                'property' => 'propertyName',
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
            'default' => [
                'expression' => new ObjectPropertyAccessExpression(
                    VariablePlaceholder::createDependency('OBJECT'),
                    'propertyName'
                ),
                'expectedString' => '{{ OBJECT }}->propertyName',
            ],
        ];
    }
}
