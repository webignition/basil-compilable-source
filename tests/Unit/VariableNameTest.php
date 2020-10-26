<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Tests\Unit;

use webignition\BasilCompilableSource\Metadata\Metadata;
use webignition\BasilCompilableSource\VariableName;

class VariableNameTest extends AbstractResolvableTest
{
    public function testGetMetadata()
    {
        $this->assertEquals(new Metadata(), (new VariableName('name'))->getMetadata());
    }

    /**
     * @dataProvider renderDataProvider
     */
    public function testRender(VariableName $placeholder, string $expectedString)
    {
        $this->assertRenderResolvable($expectedString, $placeholder);
    }

    public function renderDataProvider(): array
    {
        return [
            'empty' => [
                'placeholder' => new VariableName(''),
                'expectedString' => '$',
            ],
            'non-empty' => [
                'placeholder' => new VariableName('name'),
                'expectedString' => '$name',
            ],
        ];
    }
}
