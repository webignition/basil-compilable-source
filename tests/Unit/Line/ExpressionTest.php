<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Tests\Unit\Line;

use webignition\BasilCompilableSource\Block\ClassDependencyCollection;
use webignition\BasilCompilableSource\Line\ClassDependency;
use webignition\BasilCompilableSource\Line\EmptyLine;
use webignition\BasilCompilableSource\Line\Expression;
use webignition\BasilCompilableSource\Line\ExpressionInterface;
use webignition\BasilCompilableSource\Line\MethodInvocation\MethodInvocation;
use webignition\BasilCompilableSource\Line\MethodInvocation\ObjectMethodInvocation;
use webignition\BasilCompilableSource\LineInterface;
use webignition\BasilCompilableSource\Metadata\Metadata;
use webignition\BasilCompilableSource\Metadata\MetadataInterface;
use webignition\BasilCompilableSource\VariablePlaceholder;
use webignition\BasilCompilableSource\VariablePlaceholderCollection;

class ExpressionTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider createDataProvider
     */
    public function testCreate(LineInterface $line, ?MetadataInterface $metadata, MetadataInterface $expectedMetadata)
    {
        $expression = new Expression($line, $metadata);

        $this->assertEquals($expectedMetadata, $expression->getMetadata());
    }

    public function createDataProvider(): array
    {
        return [
            'empty' => [
                'line' => new EmptyLine(),
                'metadata' => null,
                'expectedMetadata' => new Metadata(),
            ],
            'variable dependency, no explicit metadata' => [
                'line' => VariablePlaceholder::createDependency('DEPENDENCY'),
                'metadata' => null,
                'expectedMetadata' => new Metadata([
                    Metadata::KEY_VARIABLE_DEPENDENCIES => VariablePlaceholderCollection::createDependencyCollection([
                        'DEPENDENCY',
                    ])
                ]),
            ],
            'variable export, no explicit metadata' => [
                'line' => VariablePlaceholder::createExport('EXPORT'),
                'metadata' => null,
                'expectedMetadata' => new Metadata([
                    Metadata::KEY_VARIABLE_EXPORTS => VariablePlaceholderCollection::createExportCollection([
                        'EXPORT',
                    ])
                ]),
            ],
            'variable dependency, has explicit metadata' => [
                'line' => VariablePlaceholder::createDependency('DEPENDENCY'),
                'metadata' => new Metadata([
                    Metadata::KEY_CLASS_DEPENDENCIES => new ClassDependencyCollection([
                        new ClassDependency(ClassDependency::class),
                    ])
                ]),
                'expectedMetadata' => new Metadata([
                    Metadata::KEY_CLASS_DEPENDENCIES => new ClassDependencyCollection([
                        new ClassDependency(ClassDependency::class),
                    ]),
                    Metadata::KEY_VARIABLE_DEPENDENCIES => VariablePlaceholderCollection::createDependencyCollection([
                        'DEPENDENCY',
                    ])
                ]),
            ],
            'method invocation' => [
                'line' => new MethodInvocation('methodName'),
                'metadata' => null,
                'expectedMetadata' => new Metadata(),
            ],
            'object method invocation' => [
                'line' => new ObjectMethodInvocation('object', 'methodName'),
                'metadata' => null,
                'expectedMetadata' => new Metadata(),
            ],
        ];
    }

    /**
     * @dataProvider renderDataProvider
     */
    public function testRender(ExpressionInterface $expression, string $expectedString)
    {
        $this->assertSame($expectedString, $expression->render());
    }

    public function renderDataProvider(): array
    {
        return [
            'variable placeholder' => [
                'expression' => new Expression(
                    VariablePlaceholder::createDependency('DEPENDENCY'),
                    new Metadata([
                        Metadata::KEY_VARIABLE_EXPORTS => VariablePlaceholderCollection::createDependencyCollection([
                            'DEPENDENCY',
                        ])
                    ])
                ),
                'expectedString' => '{{ DEPENDENCY }}',
            ],
            'method invocation' => [
                'expression' => new MethodInvocation('methodName'),
                'expectedString' => 'methodName()',
            ],
            'object method invocation' => [
                'expression' => new ObjectMethodInvocation('object', 'methodName'),
                'expectedString' => 'object->methodName()',
            ],
        ];
    }
}
