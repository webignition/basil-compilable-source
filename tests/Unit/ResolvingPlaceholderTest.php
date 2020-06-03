<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Tests\Unit;

use webignition\BasilCompilableSource\Metadata\Metadata;
use webignition\BasilCompilableSource\ResolvingPlaceholder;

class ResolvingPlaceholderTest extends \PHPUnit\Framework\TestCase
{
    public function testGetMetadata()
    {
        $this->assertEquals(new Metadata(), (new ResolvingPlaceholder('name'))->getMetadata());
    }

    /**
     * @dataProvider renderDataProvider
     */
    public function testRender(ResolvingPlaceholder $placeholder, string $expectedString)
    {
        $this->assertSame($expectedString, $placeholder->render());
    }

    public function renderDataProvider(): array
    {
        return [
            'empty' => [
                'placeholder' => new ResolvingPlaceholder(''),
                'expectedString' => '$',
            ],
            'non-empty' => [
                'placeholder' => new ResolvingPlaceholder('name'),
                'expectedString' => '$name',
            ],
        ];
    }
}
