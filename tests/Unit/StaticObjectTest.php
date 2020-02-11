<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Tests\Unit;

use webignition\BasilCompilableSource\Block\ClassDependencyCollection;
use webignition\BasilCompilableSource\Line\ClassDependency;
use webignition\BasilCompilableSource\Metadata\Metadata;
use webignition\BasilCompilableSource\Metadata\MetadataInterface;
use webignition\BasilCompilableSource\StaticObject;

class StaticObjectTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider getMetadataDataProvider
     */
    public function testGetMetadata(ClassDependency $classDependency, MetadataInterface $expectedMetadata)
    {
        $staticObject = new StaticObject($classDependency);

        $this->assertEquals($expectedMetadata, $staticObject->getMetadata());
    }

    public function getMetadataDataProvider(): array
    {
        return [
            'default' => [
                'classDependency' => new ClassDependency(ClassDependency::class),
                'expectedMetadata' => new Metadata([
                    Metadata::KEY_CLASS_DEPENDENCIES => new ClassDependencyCollection([
                        new ClassDependency(ClassDependency::class),
                    ]),
                ]),
            ],
        ];
    }

    /**
     * @dataProvider renderDataProvider
     */
    public function testRender(StaticObject $staticObject, string $expectedString)
    {
        $this->assertSame($expectedString, $staticObject->render());
    }

    public function renderDataProvider(): array
    {
        return [
            'default' => [
                'staticObject' => new StaticObject(
                    new ClassDependency(StaticObject::class)
                ),
                'expectedString' => 'StaticObject',
            ],
        ];
    }
}
