<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Tests\Unit\DocBlock;

use webignition\BasilCompilableSource\Annotation\ParameterAnnotation;
use webignition\BasilCompilableSource\DocBlock\DocBlock;
use webignition\BasilCompilableSource\VariableName;

class DocBlockTest extends \PHPUnit\Framework\TestCase
{
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
