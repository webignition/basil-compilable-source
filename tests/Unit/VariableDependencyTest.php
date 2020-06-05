<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Tests\Unit;

use webignition\BasilCompilableSource\Metadata\Metadata;
use webignition\BasilCompilableSource\Metadata\MetadataInterface;
use webignition\BasilCompilableSource\VariableDependency;
use webignition\BasilCompilableSource\VariableDependencyCollection;

class VariableDependencyTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider constructDataProvider
     */
    public function testConstruct(string $name)
    {
        $dependency = new VariableDependency($name);

        $this->assertSame($name, $dependency->getName());
    }

    public function constructDataProvider(): array
    {
        return [
            'default' => [
                'name' => 'DEPENDENCY',
            ],
        ];
    }

    /**
     * @dataProvider getMetadataDataProvider
     */
    public function testGetMetadata(VariableDependency $dependency, MetadataInterface $expectedMetadata)
    {
        $this->assertEquals($expectedMetadata, $dependency->getMetadata());
    }

    public function getMetadataDataProvider(): array
    {
        return [
            'variable dependency' => [
                'dependency' => new VariableDependency('DEPENDENCY'),
                'expectedMetadata' => new Metadata([
                    Metadata::KEY_VARIABLE_DEPENDENCIES => new VariableDependencyCollection([
                        'DEPENDENCY',
                    ]),
                ]),
            ],
        ];
    }

    /**
     * @dataProvider renderDataProvider
     */
    public function testRender(VariableDependency $dependency, string $expectedString)
    {
        $this->assertSame($expectedString, $dependency->render());
    }

    public function renderDataProvider(): array
    {
        return [
            'empty' => [
                'dependency' => new VariableDependency(''),
                'expectedString' => '{{  }}',
            ],
            'non-empty' => [
                'dependency' => new VariableDependency('NAME'),
                'expectedString' => '{{ NAME }}',
            ],
        ];
    }
}
