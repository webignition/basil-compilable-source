<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Tests\Unit\Block;

use webignition\BasilCompilableSource\Block\DocBlock;
use webignition\BasilCompilableSource\Tests\Services\ObjectReflector;

class DocBlockTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider createDataProvider
     *
     * @param string[] $lines
     * @param string[] $expectedLines
     */
    public function testCreate(array $lines, array $expectedLines)
    {
        $docBlock = new DocBlock($lines);

        $this->assertEquals(
            $expectedLines,
            ObjectReflector::getProperty($docBlock, 'lines')
        );
    }

    public function createDataProvider(): array
    {
        return [
            'empty' => [
                'lines' => [],
                'expectedLines' => [],
            ],
            'has lines' => [
                'lines' => [
                    new \stdClass(),
                    "\n",
                    'single line comment',
                    true,
                    1,
                ],
                'expectedLines' => [
                    "\n",
                    'single line comment',
                ],
            ],
        ];
    }

    /**
     * @dataProvider renderDataProvider
     */
    public function testRender(DocBlock $docBlock, string $expectedString)
    {
        $this->assertSame($expectedString, $docBlock->render());
    }

    public function renderDataProvider(): array
    {
        return [
            'empty' => [
                'docBlock' => new DocBlock([]),
                'expectedString' =>
                    '/**' . "\n" .
                    ' */',
            ],
            'non-empty' => [
                'docBlock' => new DocBlock([
                    "\n",
                    'single line comment',
                ]),
                'expectedString' =>
                    '/**' . "\n" .
                    ' *' . "\n" .
                    ' * single line comment' . "\n" .
                    ' */',
            ],
        ];
    }
}
