<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Tests\Unit\Expression;

use webignition\BasilCompilableSource\Expression\ObjectPropertyAccessExpression;
use webignition\BasilCompilableSource\Metadata\Metadata;
use webignition\BasilCompilableSource\Metadata\MetadataInterface;
use webignition\BasilCompilableSource\Tests\Unit\AbstractResolvableTest;
use webignition\BasilCompilableSource\VariableDependency;
use webignition\BasilCompilableSource\VariableDependencyCollection;
use webignition\BasilCompilableSource\VariableName;
use webignition\BasilCompilableSource\VariablePlaceholderInterface;

class ObjectPropertyAccessExpressionTest extends AbstractResolvableTest
{
    /**
     * @dataProvider createDataProvider
     */
    public function testCreate(
        VariablePlaceholderInterface $objectPlaceholder,
        string $property,
        MetadataInterface $expectedMetadata
    ): void {
        $invocation = new ObjectPropertyAccessExpression($objectPlaceholder, $property);

        $this->assertSame($objectPlaceholder, $invocation->getObjectPlaceholder());
        $this->assertSame($property, $invocation->getProperty());
        $this->assertEquals($expectedMetadata, $invocation->getMetadata());
    }

    /**
     * @return array<mixed>
     */
    public function createDataProvider(): array
    {
        return [
            'has resolvable placeholder' => [
                'objectPlaceholder' => new VariableDependency('OBJECT'),
                'property' => 'propertyName',
                'expectedMetadata' => new Metadata([
                    Metadata::KEY_VARIABLE_DEPENDENCIES => new VariableDependencyCollection([
                        'OBJECT',
                    ]),
                ]),
            ],
            'has resolving placeholder' => [
                'objectPlaceholder' => new VariableName('object'),
                'property' => 'propertyName',
                'expectedMetadata' => new Metadata(),
            ],
        ];
    }

    /**
     * @dataProvider renderDataProvider
     */
    public function testRender(ObjectPropertyAccessExpression $expression, string $expectedString): void
    {
        $this->assertRenderResolvable($expectedString, $expression);
    }

    /**
     * @return array<mixed>
     */
    public function renderDataProvider(): array
    {
        return [
            'has resolvable placeholder' => [
                'expression' => new ObjectPropertyAccessExpression(
                    new VariableDependency('OBJECT'),
                    'propertyName'
                ),
                'expectedString' => '{{ OBJECT }}->propertyName',
            ],
            'has resolving placeholder' => [
                'expression' => new ObjectPropertyAccessExpression(
                    new VariableName('object'),
                    'propertyName'
                ),
                'expectedString' => '$object->propertyName',
            ],
        ];
    }
}
