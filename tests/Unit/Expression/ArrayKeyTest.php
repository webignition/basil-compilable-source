<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Tests\Unit\Expression;

use webignition\BasilCompilableSource\Expression\ArrayKey;

class ArrayKeyTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider renderDataProvider
     */
    public function testRender(ArrayKey $key, string $expectedString)
    {
        $this->assertSame($expectedString, $key->render());
    }

    public function renderDataProvider(): array
    {
        return [
            'empty' => [
                'key' => new ArrayKey(''),
                'expectedString' => "''",
            ],
            'non-empty' => [
                'key' => new ArrayKey('key'),
                'expectedString' => "'key'",
            ],
        ];
    }
}
