<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Tests\Unit\Block;

use webignition\BasilCompilableSource\Block\DocBlock;
use webignition\BasilCompilableSource\Line\ClassDependency;
use webignition\BasilCompilableSource\Line\EmptyLine;
use webignition\BasilCompilableSource\Line\MethodInvocation\MethodInvocation;
use webignition\BasilCompilableSource\Line\MethodInvocation\ObjectMethodInvocation;
use webignition\BasilCompilableSource\Line\SingleLineComment;
use webignition\BasilCompilableSource\Line\Statement\AssignmentStatement;
use webignition\BasilCompilableSource\Line\Statement\Statement;
use webignition\BasilCompilableSource\LineInterface;
use webignition\BasilCompilableSource\VariablePlaceholder;

class DocBlockTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider createDataProvider
     *
     * @param LineInterface[] $lines
     * @param LineInterface[] $expectedLines
     */
    public function testCreate(array $lines, array $expectedLines)
    {
        $docBlock = new DocBlock($lines);

        $docBlockLines = [];
        foreach ($docBlock->getLines() as $line) {
            $docBlockLines[] = $line;
        }

        $this->assertEquals($expectedLines, $docBlockLines);
    }

    public function createDataProvider(): array
    {
        return [
            'empty' => [
                'lines' => [],
                'expectedLines' => [],
            ],
            'lines' => [
                'sources' => [
                    new MethodInvocation('methodName'),
                    new ObjectMethodInvocation(
                        VariablePlaceholder::createDependency('OBJECT'),
                        'methodName'
                    ),
                    new Statement(new MethodInvocation('methodName')),
                    new Statement(new ObjectMethodInvocation(
                        VariablePlaceholder::createDependency('OBJECT'),
                        'methodName'
                    )),
                    new AssignmentStatement(
                        VariablePlaceholder::createExport('PLACEHOLDER'),
                        new MethodInvocation('methodName')
                    ),
                    new ClassDependency(ClassDependency::class),
                    new EmptyLine(),
                    new SingleLineComment('single line comment'),
                ],
                'expectedLines' => [
                    new EmptyLine(),
                    new SingleLineComment('single line comment'),
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
                'docBlock' => new DocBlock(),
                'expectedString' =>
                    '/**' . "\n" .
                    ' */',
            ],
            'non-empty' => [
                'docBlock' => new DocBlock([
                    new EmptyLine(),
                    new SingleLineComment('single line comment'),
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
