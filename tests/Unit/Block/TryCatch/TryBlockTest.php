<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Tests\Unit\Block\TryCatch;

use webignition\BasilCompilableSource\Block\TryCatch\TryBlock;
use webignition\BasilCompilableSource\Line\MethodInvocation\MethodInvocation;
use webignition\BasilCompilableSource\Line\SingleLineComment;
use webignition\BasilCompilableSource\Line\Statement\Statement;

class TryBlockTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider renderDataProvider
     */
    public function testRender(TryBlock $tryBlock, string $expectedString)
    {
        $this->assertSame($expectedString, $tryBlock->render());
    }

    public function renderDataProvider(): array
    {
        return [
            'empty' => [
                'tryBlock' => new TryBlock(),
                'expectedString' =>
                    'try {' . "\n" .
                    "\n" .
                    '}',
            ],
            'non-empty' => [
                'tryBlock' => new TryBlock([
                    new SingleLineComment('single line comment'),
                    new Statement(new MethodInvocation('methodName')),
                ]),
                'expectedString' =>
                    'try {' . "\n" .
                    '    // single line comment' . "\n" .
                    '    methodName();' . "\n" .
                    '}',
            ],
        ];
    }
}
