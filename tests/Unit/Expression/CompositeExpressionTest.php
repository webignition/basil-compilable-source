<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Tests\Unit\Expression;

use webignition\BasilCompilableSource\Expression\CastExpression;
use webignition\BasilCompilableSource\Expression\CompositeExpression;
use webignition\BasilCompilableSource\Expression\LiteralExpression;
use webignition\BasilCompilableSource\Metadata\Metadata;
use webignition\BasilCompilableSource\Metadata\MetadataInterface;
use webignition\BasilCompilableSource\Tests\Unit\AbstractResolvableTest;
use webignition\BasilCompilableSource\VariableDependency;
use webignition\BasilCompilableSource\VariableDependencyCollection;

class CompositeExpressionTest extends AbstractResolvableTest
{
    /**
     * @dataProvider createDataProvider
     *
     * @param array<mixed> $expressions
     */
    public function testCreate(array $expressions, MetadataInterface $expectedMetadata): void
    {
        $expression = new CompositeExpression($expressions);

        $this->assertEquals($expectedMetadata, $expression->getMetadata());
    }

    /**
     * @return array<mixed>
     */
    public function createDataProvider(): array
    {
        return [
            'empty' => [
                'expressions' => [],
                'expectedMetadata' => new Metadata(),
            ],
            'variable dependency' => [
                'expressions' => [
                    new VariableDependency('DEPENDENCY'),
                ],
                'expectedMetadata' => new Metadata([
                    Metadata::KEY_VARIABLE_DEPENDENCIES => new VariableDependencyCollection([
                        'DEPENDENCY',
                    ]),
                ]),
            ],
            'variable dependency and array access' => [
                'expressions' => [
                    new VariableDependency('ENV'),
                    new LiteralExpression('[\'KEY\']')
                ],
                'expectedMetadata' => new Metadata([
                    Metadata::KEY_VARIABLE_DEPENDENCIES => new VariableDependencyCollection([
                        'ENV',
                    ]),
                ]),
            ],
        ];
    }

    /**
     * @dataProvider renderDataProvider
     */
    public function testRender(CompositeExpression $expression, string $expectedString): void
    {
        $this->assertRenderResolvable($expectedString, $expression);
    }

    /**
     * @return array<mixed>
     */
    public function renderDataProvider(): array
    {
        return [
            'empty' => [
                'expression' => new CompositeExpression([]),
                'expectedString' => '',
            ],
            'single literal' => [
                'expression' => new CompositeExpression([
                    new LiteralExpression('literal1'),
                ]),
                'expectedString' => 'literal1',
            ],
            'multiple literals' => [
                'expression' => new CompositeExpression([
                    new LiteralExpression('literal1'),
                    new LiteralExpression('literal2'),
                    new LiteralExpression('literal3'),
                ]),
                'expectedString' => 'literal1literal2literal3',
            ],
            'variable dependency' => [
                'expression' => new CompositeExpression([
                    new VariableDependency('DEPENDENCY'),
                ]),
                'expectedString' => '{{ DEPENDENCY }}',
            ],
            'variable dependency and array access' => [
                'expression' => new CompositeExpression([
                    new VariableDependency('ENV'),
                    new LiteralExpression('[\'KEY\']')
                ]),
                'expectedString' => '{{ ENV }}[\'KEY\']',
            ],
            'resolvable expression, stringable expression, resolvable expression' => [
                'expression' => new CompositeExpression([
                    new CastExpression(
                        new LiteralExpression('1'),
                        'string'
                    ),
                    new LiteralExpression(' . \'x\' . '),
                    new CastExpression(
                        new LiteralExpression('2'),
                        'string'
                    ),
                ]),
                'expectedString' => '(string) (1) . \'x\' . (string) (2)',
            ],
        ];
    }
}
