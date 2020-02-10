<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Tests\Unit;

use webignition\BasilCompilableSource\VariablePlaceholder;

class VariablePlaceholderTest extends \PHPUnit\Framework\TestCase
{
    public function testCreate()
    {
        $name = 'NAME';
        $placeholder = new VariablePlaceholder($name);

        $this->assertSame($name, $placeholder->getContent());
    }

    /**
     * @dataProvider renderDataProvider
     */
    public function testRender(VariablePlaceholder $placeholder, string $expectedString)
    {
        $this->assertSame($expectedString, $placeholder->render());
    }

    public function renderDataProvider(): array
    {
        return [
            'empty' => [
                'placeholder' => new VariablePlaceholder(''),
                'expectedString' => '{{  }}',
            ],
            'non-empty' => [
                'placeholder' => new VariablePlaceholder('NAME'),
                'expectedString' => '{{ NAME }}',
            ],
        ];
    }
}
