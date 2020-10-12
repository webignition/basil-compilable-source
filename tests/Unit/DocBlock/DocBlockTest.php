<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Tests\Unit\DocBlock;

use webignition\BasilCompilableSource\Annotation\ParameterAnnotation;
use webignition\BasilCompilableSource\DocBlock\DocBlock;
use webignition\BasilCompilableSource\VariableName;

class DocBlockTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider mergeDataProvider
     * @dataProvider appendDataProvider
     */
    public function testAppend(DocBlock $docBlock, DocBlock $merge, DocBlock $expectedDocBlock)
    {
        $this->assertEquals($expectedDocBlock, $docBlock->append($merge));
    }

    public function appendDataProvider(): array
    {
        return [
            'append: non-empty, non-empty' => [
                'docBlock' => new DocBlock([
                    'docBlock line',
                ]),
                'merge' => new DocBlock([
                    'merge line',
                ]),
                'expectedDocBlock' => new DocBlock([
                    'docBlock line',
                    'merge line',
                ]),
            ],
        ];
    }

    /**
     * @dataProvider mergeDataProvider
     * @dataProvider prependDataProvider
     */
    public function testPrepend(DocBlock $docBlock, DocBlock $merge, DocBlock $expectedDocBlock)
    {
        $this->assertEquals($expectedDocBlock, $docBlock->prepend($merge));
    }

    public function prependDataProvider(): array
    {
        return [
            'prepend: non-empty, non-empty' => [
                'docBlock' => new DocBlock([
                    'docBlock line',
                ]),
                'merge' => new DocBlock([
                    'merge line',
                ]),
                'expectedDocBlock' => new DocBlock([
                    'merge line',
                    'docBlock line',
                ]),
            ],
        ];
    }

    public function mergeDataProvider(): array
    {
        return [
            'merge: empty, empty' => [
                'docBlock' => new DocBlock([]),
                'merge' => new DocBlock([]),
                'expectedDocBlock' => new DocBlock([]),
            ],
            'merge: non-empty, empty' => [
                'docBlock' => new DocBlock([
                    'docBlock line',
                ]),
                'merge' => new DocBlock([]),
                'expectedDocBlock' => new DocBlock([
                    'docBlock line',
                ]),
            ],
            'merge: empty, non-empty' => [
                'docBlock' => new DocBlock([]),
                'merge' => new DocBlock([
                    'merge line',
                ]),
                'expectedDocBlock' => new DocBlock([
                    'merge line',
                ]),
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
                    new ParameterAnnotation('string', new VariableName('name'))
                ]),
                'expectedString' =>
                    '/**' . "\n" .
                    ' *' . "\n" .
                    ' * single line comment' . "\n" .
                    ' * @param string $name' . "\n" .
                    ' */',
            ],
        ];
    }
}
