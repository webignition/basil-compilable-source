<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Tests\Unit\Line;

use webignition\BasilCompilableSource\Line\CompositeExpression;
use webignition\BasilCompilableSource\Line\LiteralExpression;
use webignition\BasilCompilableSource\Metadata\Metadata;
use webignition\BasilCompilableSource\Metadata\MetadataInterface;
use webignition\BasilCompilableSource\VariableDependency;
use webignition\BasilCompilableSource\VariablePlaceholderCollection;

class CompositeExpressionTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider createDataProvider
     *
     * @param array<mixed> $expressions
     * @param MetadataInterface $expectedMetadata
     */
    public function testCreate(array $expressions, MetadataInterface $expectedMetadata)
    {
        $expression = new CompositeExpression($expressions);

        $this->assertEquals($expectedMetadata, $expression->getMetadata());
    }

    public function createDataProvider(): array
    {
        return [
            'empty' => [
                'expressions' => [],
                'expectedMetadata' => new Metadata(),
            ],
            'variable dependency' => [
                'expressions' => [
                    VariableDependency::createDependency('DEPENDENCY'),
                ],
                'expectedMetadata' => new Metadata([
                    Metadata::KEY_VARIABLE_DEPENDENCIES => VariablePlaceholderCollection::createDependencyCollection([
                        'DEPENDENCY',
                    ]),
                ]),
            ],
            'variable export' => [
                'expressions' => [
                    VariableDependency::createExport('EXPORT'),
                ],
                'expectedMetadata' => new Metadata([
                    Metadata::KEY_VARIABLE_EXPORTS => VariablePlaceholderCollection::createExportCollection([
                        'EXPORT',
                    ]),
                ]),
            ],
            'variable dependency and variable export' => [
                'expressions' => [
                    VariableDependency::createDependency('DEPENDENCY'),
                    VariableDependency::createExport('EXPORT'),
                ],
                'expectedMetadata' => new Metadata([
                    Metadata::KEY_VARIABLE_DEPENDENCIES => VariablePlaceholderCollection::createDependencyCollection([
                        'DEPENDENCY',
                    ]),
                    Metadata::KEY_VARIABLE_EXPORTS => VariablePlaceholderCollection::createExportCollection([
                        'EXPORT',
                    ]),
                ]),
            ],
            'variable dependency and array access' => [
                'expressions' => [
                    VariableDependency::createDependency('ENV'),
                    new LiteralExpression('[\'KEY\']')
                ],
                'expectedMetadata' => new Metadata([
                    Metadata::KEY_VARIABLE_DEPENDENCIES => VariablePlaceholderCollection::createDependencyCollection([
                        'ENV',
                    ]),
                ]),
            ],
        ];
    }

    /**
     * @dataProvider renderDataProvider
     */
    public function testRender(CompositeExpression $expression, string $expectedString)
    {
        $this->assertSame($expectedString, $expression->render());
    }

    public function renderDataProvider(): array
    {
        return [
            'empty' => [
                'expressions' => new CompositeExpression([]),
                'expectedString' => '',
            ],
            'variable dependency' => [
                'expressions' => new CompositeExpression([
                    VariableDependency::createDependency('DEPENDENCY'),
                ]),
                'expectedString' => '{{ DEPENDENCY }}',
            ],
            'variable export' => [
                'expressions' => new CompositeExpression([
                    VariableDependency::createExport('EXPORT'),
                ]),
                'expectedString' => '{{ EXPORT }}',
            ],
            'variable dependency and variable export' => [
                'expressions' => new CompositeExpression([
                    VariableDependency::createDependency('DEPENDENCY'),
                    VariableDependency::createExport('EXPORT'),
                ]),
                'expectedString' => '{{ DEPENDENCY }}{{ EXPORT }}',
            ],
            'variable dependency and array access' => [
                'expressions' => new CompositeExpression([
                    VariableDependency::createDependency('ENV'),
                    new LiteralExpression('[\'KEY\']')
                ]),
                'expectedString' => '{{ ENV }}[\'KEY\']',
            ],
        ];
    }
}
