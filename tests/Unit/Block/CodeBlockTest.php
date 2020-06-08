<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Tests\Unit\Block;

use webignition\BasilCompilableSource\Block\CodeBlock;
use webignition\BasilCompilableSource\Block\CodeBlockInterface;
use webignition\BasilCompilableSource\Body\Body;
use webignition\BasilCompilableSource\Body\BodyInterface;
use webignition\BasilCompilableSource\Line\CastExpression;
use webignition\BasilCompilableSource\Line\ClassDependency;
use webignition\BasilCompilableSource\Line\ClosureExpression;
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
use webignition\BasilCompilableSource\VariableDependency;
use webignition\BasilCompilableSource\VariableDependencyCollection;
use webignition\BasilCompilableSource\VariableName;

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
                        new VariableDependency('OBJECT'),
                        'methodName'
                    ),
                    new Statement(new MethodInvocation('methodName')),
                    new Statement(new ObjectMethodInvocation(
                        new VariableDependency('OBJECT'),
                        'methodName'
                    )),
                    new AssignmentStatement(
                        new VariableName('variable'),
                        new MethodInvocation('methodName')
                    ),
                    new ClassDependency(ClassDependency::class),
                    new EmptyLine(),
                    new SingleLineComment('single line comment'),
                ],
                'expectedLines' => [
                    new Statement(new MethodInvocation('methodName')),
                    new Statement(new ObjectMethodInvocation(
                        new VariableDependency('OBJECT'),
                        'methodName'
                    )),
                    new AssignmentStatement(
                        new VariableName('variable'),
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
                                new VariableDependency('OBJECT'),
                                'methodName'
                            )
                        ),
                        new AssignmentStatement(
                            new VariableName('variable'),
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
                            new VariableDependency('OBJECT'),
                            'methodName'
                        )
                    ),
                    new AssignmentStatement(
                        new VariableName('variable'),
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
                            new VariableDependency('DEPENDENCY'),
                            'methodName'
                        )
                    ),
                    new AssignmentStatement(
                        new VariableName('variable'),
                        new MethodInvocation('methodName')
                    ),
                ]),
                'expectedMetadata' => new Metadata([
                    Metadata::KEY_VARIABLE_DEPENDENCIES => new VariableDependencyCollection([
                        'DEPENDENCY',
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
//            'empty' => [
//                'codeBlock' => new CodeBlock(),
//                'expectedString' => '',
//            ],
            'lines' => [
                'codeBlock' => new CodeBlock([
                    new Statement(new MethodInvocation('methodName')),
                    new Statement(new ObjectMethodInvocation(
                        new VariableDependency('OBJECT'),
                        'methodName'
                    )),
                    new AssignmentStatement(
                        new VariableName('variable'),
                        new MethodInvocation('methodName')
                    ),
                    new AssignmentStatement(
                        new VariableName('variable'),
                        new CastExpression(
                            new MethodInvocation('methodName', [], MethodInvocation::ARGUMENT_FORMAT_INLINE),
                            'string'
                        )
                    ),
                    new AssignmentStatement(
                        new VariableName('variable'),
                        new StaticObjectMethodInvocation(
                            new StaticObject(
                                ClassDependency::class
                            ),
                            'methodName'
                        )
                    ),
                    new AssignmentStatement(
                        new VariableName('variable'),
                        new ObjectMethodInvocation(
                            new VariableDependency('OBJECT'),
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
                        new VariableName('variable'),
                        new ObjectMethodInvocation(
                            new VariableDependency('OBJECT'),
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
                    '$variable = methodName();' . "\n" .
                    '$variable = (string) (methodName());' . "\n" .
                    '$variable = ClassDependency::methodName();' . "\n" .
                    '$variable = {{ OBJECT }}->methodName(\'string\', EmptyLine::innerMethodName());' . "\n" .
                    '$variable = {{ OBJECT }}->methodName(ClassDependency::innerMethodName(' . "\n" .
                    '    \'string1\',' . "\n" .
                    '    \'string2\'' . "\n" .
                    '));' . "\n" .
                    '' . "\n" .
                    '// single line comment'
                ,
            ],
        ];
    }

    /**
     * @dataProvider createEnclosingCodeBlockDataProvider
     */
    public function testCreateEnclosingCodeBlock(BodyInterface $body, CodeBlockInterface $expectedCodeBlock)
    {
        $this->assertEquals($expectedCodeBlock, CodeBlock::createEnclosingCodeBlock($body));
    }

    public function createEnclosingCodeBlockDataProvider(): array
    {
        return [
            'enclose a code block' => [
                'body' => new Body([
                    new Statement(
                        new LiteralExpression('"literal')
                    ),
                ]),
                'expectedCodeBlock' => new CodeBlock([
                    new Statement(
                        new ClosureExpression(
                            new Body([
                                new Statement(
                                    new LiteralExpression('"literal')
                                ),
                            ])
                        )
                    ),
                ]),
            ],
        ];
    }
}
