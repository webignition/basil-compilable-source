<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Tests\Unit\Line;

use webignition\BasilCompilableSource\Line\ObjectPropertyAccessExpression;
use webignition\BasilCompilableSource\Metadata\Metadata;
use webignition\BasilCompilableSource\Metadata\MetadataInterface;
use webignition\BasilCompilableSource\ResolvablePlaceholder;
use webignition\BasilCompilableSource\ResolvablePlaceholderCollection;
use webignition\BasilCompilableSource\ResolvingPlaceholder;
use webignition\BasilCompilableSource\VariablePlaceholderInterface;

class ObjectPropertyAccessExpressionTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider createDataProvider
     */
    public function testCreate(
        VariablePlaceholderInterface $objectPlaceholder,
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
            'has resolvable placeholder' => [
                'objectPlaceholder' => ResolvablePlaceholder::createDependency('OBJECT'),
                'property' => 'propertyName',
                'expectedMetadata' => new Metadata([
                    Metadata::KEY_VARIABLE_DEPENDENCIES => ResolvablePlaceholderCollection::createDependencyCollection([
                        'OBJECT',
                    ]),
                ]),
            ],
            'has resolving placeholder' => [
                'objectPlaceholder' => new ResolvingPlaceholder('object'),
                'property' => 'propertyName',
                'expectedMetadata' => new Metadata(),
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
            'has resolvable placeholder' => [
                'expression' => new ObjectPropertyAccessExpression(
                    ResolvablePlaceholder::createDependency('OBJECT'),
                    'propertyName'
                ),
                'expectedString' => '{{ OBJECT }}->propertyName',
            ],
            'has resolving placeholder' => [
                'expression' => new ObjectPropertyAccessExpression(
                    new ResolvingPlaceholder('object'),
                    'propertyName'
                ),
                'expectedString' => '$object->propertyName',
            ],
        ];
    }
}
