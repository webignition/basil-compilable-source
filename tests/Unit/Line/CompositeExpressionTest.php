<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Tests\Unit\Line;

use webignition\BasilCompilableSource\Line\CompositeExpression;
use webignition\BasilCompilableSource\Line\LiteralExpression;
use webignition\BasilCompilableSource\Metadata\Metadata;
use webignition\BasilCompilableSource\Metadata\MetadataInterface;
use webignition\BasilCompilableSource\VariablePlaceholder;
use webignition\BasilCompilableSource\VariablePlaceholderCollection;

class CompositeExpressionTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider createDataProvider
     *
     * @param array<mixed> $expressions
     * @param MetadataInterface $expectedMetadata
     */
    public function testCreate(array $expressions, ?string $castTo, MetadataInterface $expectedMetadata)
    {
        $expression = new CompositeExpression($expressions, $castTo);

        $this->assertEquals($expectedMetadata, $expression->getMetadata());
        $this->assertSame($castTo, $expression->getCastTo());
    }

    public function createDataProvider(): array
    {
        return [
            'empty' => [
                'expressions' => [],
                'castTo' => null,
                'expectedMetadata' => new Metadata(),
            ],
            'variable dependency' => [
                'expressions' => [
                    VariablePlaceholder::createDependency('DEPENDENCY'),
                ],
                'castTo' => null,
                'expectedMetadata' => new Metadata([
                    Metadata::KEY_VARIABLE_DEPENDENCIES => VariablePlaceholderCollection::createDependencyCollection([
                        'DEPENDENCY',
                    ]),
                ]),
            ],
            'variable dependency, cast to string' => [
                'expressions' => [
                    VariablePlaceholder::createDependency('DEPENDENCY'),
                ],
                'castTo' => 'string',
                'expectedMetadata' => new Metadata([
                    Metadata::KEY_VARIABLE_DEPENDENCIES => VariablePlaceholderCollection::createDependencyCollection([
                        'DEPENDENCY',
                    ]),
                ]),
            ],
            'variable export' => [
                'expressions' => [
                    VariablePlaceholder::createExport('EXPORT'),
                ],
                'castTo' => null,
                'expectedMetadata' => new Metadata([
                    Metadata::KEY_VARIABLE_EXPORTS => VariablePlaceholderCollection::createExportCollection([
                        'EXPORT',
                    ]),
                ]),
            ],
            'variable dependency and variable export' => [
                'expressions' => [
                    VariablePlaceholder::createDependency('DEPENDENCY'),
                    VariablePlaceholder::createExport('EXPORT'),
                ],
                'castTo' => null,
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
                    VariablePlaceholder::createDependency('ENV'),
                    new LiteralExpression('[\'KEY\']')
                ],
                'castTo' => null,
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
                    VariablePlaceholder::createDependency('DEPENDENCY'),
                ]),
                'expectedString' => '{{ DEPENDENCY }}',
            ],
            'variable dependency, cast to string' => [
                'expressions' => new CompositeExpression(
                    [
                        VariablePlaceholder::createDependency('DEPENDENCY'),
                    ],
                    'string'
                ),
                'expectedString' => '(string) {{ DEPENDENCY }}',
            ],
            'variable export' => [
                'expressions' => new CompositeExpression([
                    VariablePlaceholder::createExport('EXPORT'),
                ]),
                'expectedString' => '{{ EXPORT }}',
            ],
            'variable dependency and variable export' => [
                'expressions' => new CompositeExpression([
                    VariablePlaceholder::createDependency('DEPENDENCY'),
                    VariablePlaceholder::createExport('EXPORT'),
                ]),
                'expectedString' => '{{ DEPENDENCY }}{{ EXPORT }}',
            ],
            'variable dependency and array access' => [
                'expressions' => new CompositeExpression([
                    VariablePlaceholder::createDependency('ENV'),
                    new LiteralExpression('[\'KEY\']')
                ]),
                'expectedString' => '{{ ENV }}[\'KEY\']',
            ],
        ];
    }
}
