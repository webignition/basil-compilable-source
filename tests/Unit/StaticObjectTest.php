<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Tests\Unit;

use webignition\BasilCompilableSource\Block\ClassDependencyCollection;
use webignition\BasilCompilableSource\ClassName;
use webignition\BasilCompilableSource\Metadata\Metadata;
use webignition\BasilCompilableSource\Metadata\MetadataInterface;
use webignition\BasilCompilableSource\StaticObject;

class StaticObjectTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider getMetadataDataProvider
     */
    public function testGetMetadata(string $object, MetadataInterface $expectedMetadata)
    {
        $staticObject = new StaticObject($object);

        $this->assertEquals($expectedMetadata, $staticObject->getMetadata());
    }

    public function getMetadataDataProvider(): array
    {
        return [
            'string reference' => [
                'object' => 'parent',
                'expectedMetadata' => new Metadata(),
            ],
            'global classname' => [
                'object' => \StdClass::class,
                'expectedMetadata' => new Metadata([
                    Metadata::KEY_CLASS_DEPENDENCIES => new ClassDependencyCollection([
                        new ClassName(\StdClass::class),
                    ]),
                ]),
            ],
            'namespaced classname' => [
                'object' => ClassName::class,
                'expectedMetadata' => new Metadata([
                    Metadata::KEY_CLASS_DEPENDENCIES => new ClassDependencyCollection([
                        new ClassName(ClassName::class),
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
            'string reference' => [
                'object' => new StaticObject('parent'),
                'expectedString' => 'parent',
            ],
            'root-namespaced class' => [
                'object' => new StaticObject(\StdClass::class),
                'expectedString' => '\StdClass',
            ],
            'namespaced class' => [
                'object' => new StaticObject(ClassName::class),
                'expectedString' => 'ClassName',
            ],
        ];
    }
}
