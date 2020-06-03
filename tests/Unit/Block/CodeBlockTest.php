<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Tests\Unit\Block;

use webignition\BasilCompilableSource\Block\CodeBlock;
use webignition\BasilCompilableSource\Block\CodeBlockInterface;
use webignition\BasilCompilableSource\Line\CastExpression;
use webignition\BasilCompilableSource\Line\ClassDependency;
use webignition\BasilCompilableSource\Line\EmptyLine;
use webignition\BasilCompilableSource\Line\LiteralExpression;
use webignition\BasilCompilableSource\Line\MethodInvocation\MethodInvocation;
use webignition\BasilCompilableSource\Line\MethodInvocation\ObjectMethodInvocation;
use webignition\BasilCompilableSource\Line\MethodInvocation\StaticObjectMethodInvocation;
use webignition\BasilCompilableSource\Line\SingleLineComment;
use webignition\BasilCompilableSource\Line\Statement\AssignmentStatement;
use webignition\BasilCompilableSource\Line\Statement\Statement;
use webignition\BasilCompilableSource\LineInterface;
use webignition\BasilCompilableSource\Metadata\Metadata;
use webignition\BasilCompilableSource\Metadata\MetadataInterface;
use webignition\BasilCompilableSource\SourceInterface;
use webignition\BasilCompilableSource\StaticObject;
use webignition\BasilCompilableSource\VariablePlaceholder;
use webignition\BasilCompilableSource\VariablePlaceholderCollection;

class CodeBlockTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider createDataProvider
     *
     * @param SourceInterface[] $sources
     * @param LineInterface[] $expectedLines
     */
    public function testCreate(array $sources, array $expectedLines)
    {
        $codeBlock = new CodeBlock($sources);

        $lines = [];
        foreach ($codeBlock as $line) {
            $lines[] = $line;
        }

        $this->assertEquals($expectedLines, $lines);
    }

    public function createDataProvider(): array
    {
        return [
            'empty' => [
                'sources' => [],
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
                    new Statement(new MethodInvocation('methodName')),
                    new Statement(new ObjectMethodInvocation(
                        VariablePlaceholder::createDependency('OBJECT'),
                        'methodName'
                    )),
                    new AssignmentStatement(
                        VariablePlaceholder::createExport('PLACEHOLDER'),
                        new MethodInvocation('methodName')
                    ),
                    new EmptyLine(),
                    new SingleLineComment('single line comment'),
                ],
            ],
            'code blocks' => [
                'sources' => [
                    new CodeBlock([
                        new Statement(new MethodInvocation('methodName')),
                        new Statement(
                            new ObjectMethodInvocation(
                                VariablePlaceholder::createDependency('OBJECT'),
                                'methodName'
                            )
                        ),
                        new AssignmentStatement(
                            VariablePlaceholder::createExport('PLACEHOLDER'),
                            new MethodInvocation('methodName')
                        ),
                    ]),
                    new CodeBlock([
                        new EmptyLine(),
                        new SingleLineComment('single line comment'),
                    ]),
                ],
                'expectedLines' => [
                    new Statement(new MethodInvocation('methodName')),
                    new Statement(
                        new ObjectMethodInvocation(
                            VariablePlaceholder::createDependency('OBJECT'),
                            'methodName'
                        )
                    ),
                    new AssignmentStatement(
                        VariablePlaceholder::createExport('PLACEHOLDER'),
                        new MethodInvocation('methodName')
                    ),
                    new EmptyLine(),
                    new SingleLineComment('single line comment'),
                ],
            ],
        ];
    }

    /**
     * @dataProvider getMetadataDataProvider
     */
    public function testGetMetadata(CodeBlockInterface $codeBlock, MetadataInterface $expectedMetadata)
    {
        $this->assertEquals($expectedMetadata, $codeBlock->getMetadata());
    }

    public function getMetadataDataProvider(): array
    {
        return [
            'empty' => [
                'codeBlock' => new CodeBlock(),
                'expectedMetadata' => new Metadata(),
            ],
            'lines without metadata' => [
                'codeBlock' => new CodeBlock([
                    new EmptyLine(),
                    new SingleLineComment('single line comment'),
                ]),
                'expectedMetadata' => new Metadata(),
            ],
            'lines with metadata' => [
                'codeBlock' => new CodeBlock([
                    new Statement(
                        new ObjectMethodInvocation(
                            VariablePlaceholder::createDependency('DEPENDENCY'),
                            'methodName'
                        )
                    ),
                    new AssignmentStatement(
                        VariablePlaceholder::createExport('PLACEHOLDER'),
                        new MethodInvocation('methodName')
                    ),
                ]),
                'expectedMetadata' => new Metadata([
                    Metadata::KEY_VARIABLE_DEPENDENCIES => VariablePlaceholderCollection::createDependencyCollection([
                        'DEPENDENCY',
                    ]),
                    Metadata::KEY_VARIABLE_EXPORTS => VariablePlaceholderCollection::createExportCollection([
                        'PLACEHOLDER',
                    ]),
                ]),
            ],
        ];
    }

    /**
     * @dataProvider renderDataProvider
     */
    public function testRender(CodeBlockInterface $codeBlock, string $expectedString)
    {
        $this->assertSame($expectedString, $codeBlock->render());
    }

    public function renderDataProvider(): array
    {
        return [
            'empty' => [
                'codeBlock' => new CodeBlock(),
                'expectedString' => '',
            ],
            'lines' => [
                'codeBlock' => new CodeBlock([
                    new Statement(new MethodInvocation('methodName')),
                    new Statement(new ObjectMethodInvocation(
                        VariablePlaceholder::createDependency('OBJECT'),
                        'methodName'
                    )),
                    new AssignmentStatement(
                        VariablePlaceholder::createExport('PLACEHOLDER'),
                        new MethodInvocation('methodName')
                    ),
                    new AssignmentStatement(
                        VariablePlaceholder::createExport('PLACEHOLDER'),
                        new CastExpression(
                            new MethodInvocation('methodName', [], MethodInvocation::ARGUMENT_FORMAT_INLINE),
                            'string'
                        )
                    ),
                    new AssignmentStatement(
                        VariablePlaceholder::createExport('PLACEHOLDER'),
                        new StaticObjectMethodInvocation(
                            new StaticObject(
                                ClassDependency::class
                            ),
                            'methodName'
                        )
                    ),
                    new AssignmentStatement(
                        VariablePlaceholder::createExport('PLACEHOLDER'),
                        new ObjectMethodInvocation(
                            VariablePlaceholder::createDependency('OBJECT'),
                            'methodName',
                            [
                                new LiteralExpression('\'string\''),
                                new StaticObjectMethodInvocation(
                                    new StaticObject(EmptyLine::class),
                                    'innerMethodName'
                                ),
                            ]
                        )
                    ),
                    new AssignmentStatement(
                        VariablePlaceholder::createExport('PLACEHOLDER'),
                        new ObjectMethodInvocation(
                            VariablePlaceholder::createDependency('OBJECT'),
                            'methodName',
                            [
                                new StaticObjectMethodInvocation(
                                    new StaticObject(ClassDependency::class),
                                    'innerMethodName',
                                    [
                                        new LiteralExpression("'string1'"),
                                        new LiteralExpression("'string2'"),
                                    ],
                                    MethodInvocation::ARGUMENT_FORMAT_STACKED
                                )
                            ]
                        )
                    ),
                    new EmptyLine(),
                    new SingleLineComment('single line comment'),
                ]),
                'expectedString' =>
                    'methodName();' . "\n" .
                    '{{ OBJECT }}->methodName();' . "\n" .
                    '{{ PLACEHOLDER }} = methodName();' . "\n" .
                    '{{ PLACEHOLDER }} = (string) (methodName());' . "\n" .
                    '{{ PLACEHOLDER }} = ClassDependency::methodName();' . "\n" .
                    '{{ PLACEHOLDER }} = {{ OBJECT }}->methodName(\'string\', EmptyLine::innerMethodName());' . "\n" .
                    '{{ PLACEHOLDER }} = {{ OBJECT }}->methodName(ClassDependency::innerMethodName(' . "\n" .
                    '    \'string1\',' . "\n" .
                    '    \'string2\'' . "\n" .
                    '));' . "\n" .
                    '' . "\n" .
                    '// single line comment'
                ,
            ],
        ];
    }

    public function testIsEmpty()
    {
        $codeBlock = new CodeBlock();
        $this->assertTrue($codeBlock->isEmpty());

        $codeBlock = new CodeBlock([
            new SingleLineComment('comment')
        ]);

        $this->assertFalse($codeBlock->isEmpty());
    }
}
