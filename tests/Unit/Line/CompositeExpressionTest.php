<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Tests\Unit\Line;

use webignition\BasilCompilableSource\Line\CompositeExpression;
use webignition\BasilCompilableSource\Line\LiteralExpression;
use webignition\BasilCompilableSource\Metadata\Metadata;
use webignition\BasilCompilableSource\Metadata\MetadataInterface;
use webignition\BasilCompilableSource\ResolvablePlaceholder;
use webignition\BasilCompilableSource\ResolvablePlaceholderCollection;

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
                    ResolvablePlaceholder::createDependency('DEPENDENCY'),
                ],
                'expectedMetadata' => new Metadata([
                    Metadata::KEY_VARIABLE_DEPENDENCIES => ResolvablePlaceholderCollection::createDependencyCollection([
                        'DEPENDENCY',
                    ]),
                ]),
            ],
            'variable export' => [
                'expressions' => [
                    ResolvablePlaceholder::createExport('EXPORT'),
                ],
                'expectedMetadata' => new Metadata([
                    Metadata::KEY_VARIABLE_EXPORTS => ResolvablePlaceholderCollection::createExportCollection([
                        'EXPORT',
                    ]),
                ]),
            ],
            'variable dependency and variable export' => [
                'expressions' => [
                    ResolvablePlaceholder::createDependency('DEPENDENCY'),
                    ResolvablePlaceholder::createExport('EXPORT'),
                ],
                'expectedMetadata' => new Metadata([
                    Metadata::KEY_VARIABLE_DEPENDENCIES => ResolvablePlaceholderCollection::createDependencyCollection([
                        'DEPENDENCY',
                    ]),
                    Metadata::KEY_VARIABLE_EXPORTS => ResolvablePlaceholderCollection::createExportCollection([
                        'EXPORT',
                    ]),
                ]),
            ],
            'variable dependency and array access' => [
                'expressions' => [
                    ResolvablePlaceholder::createDependency('ENV'),
                    new LiteralExpression('[\'KEY\']')
                ],
                'expectedMetadata' => new Metadata([
                    Metadata::KEY_VARIABLE_DEPENDENCIES => ResolvablePlaceholderCollection::createDependencyCollection([
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
                    ResolvablePlaceholder::createDependency('DEPENDENCY'),
                ]),
                'expectedString' => '{{ DEPENDENCY }}',
            ],
            'variable export' => [
                'expressions' => new CompositeExpression([
                    ResolvablePlaceholder::createExport('EXPORT'),
                ]),
                'expectedString' => '{{ EXPORT }}',
            ],
            'variable dependency and variable export' => [
                'expressions' => new CompositeExpression([
                    ResolvablePlaceholder::createDependency('DEPENDENCY'),
                    ResolvablePlaceholder::createExport('EXPORT'),
                ]),
                'expectedString' => '{{ DEPENDENCY }}{{ EXPORT }}',
            ],
            'variable dependency and array access' => [
                'expressions' => new CompositeExpression([
                    ResolvablePlaceholder::createDependency('ENV'),
                    new LiteralExpression('[\'KEY\']')
                ]),
                'expectedString' => '{{ ENV }}[\'KEY\']',
            ],
        ];
    }
}
