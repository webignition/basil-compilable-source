<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Tests\Unit\Expression;

use webignition\BasilCompilableSource\Expression\ArrayExpression;
use webignition\BasilCompilableSource\Expression\ArrayKey;
use webignition\BasilCompilableSource\Expression\ArrayPair;
use webignition\BasilCompilableSource\Expression\LiteralExpression;
use webignition\BasilCompilableSource\Metadata\Metadata;
use webignition\BasilCompilableSource\Metadata\MetadataInterface;
use webignition\BasilCompilableSource\MethodInvocation\ObjectMethodInvocation;
use webignition\BasilCompilableSource\Tests\Unit\AbstractResolvableTest;
use webignition\BasilCompilableSource\VariableDependency;
use webignition\BasilCompilableSource\VariableDependencyCollection;

class ArrayPairTest extends AbstractResolvableTest
{
    /**
     * @dataProvider getMetadataDataProvider
     */
    public function testGetMetadata(ArrayPair $pair, MetadataInterface $expectedMetadata): void
    {
        self::assertEquals($expectedMetadata, $pair->getMetadata());
    }

    /**
     * @return array[]
     */
    public function getMetadataDataProvider(): array
    {
        return [
            'no metadata' => [
                'pair' => new ArrayPair(
                    new ArrayKey(''),
                    new LiteralExpression('\'\'')
                ),
                'expectedMetadata' => new Metadata(),
            ],
            'has metadata' => [
                'pair' => new ArrayPair(
                    new ArrayKey(''),
                    new ObjectMethodInvocation(
                        new VariableDependency('OBJECT'),
                        'methodName'
                    )
                ),
                'expectedMetadata' => new Metadata([
                    Metadata::KEY_VARIABLE_DEPENDENCIES => new VariableDependencyCollection([
                        'OBJECT',
                    ]),
                ]),
            ],
        ];
    }

    /**
     * @dataProvider renderDataProvider
     */
    public function testRender(ArrayPair $pair, string $expectedString): void
    {
        $this->assertRenderResolvable($expectedString, $pair);
    }

    /**
     * @return array[]
     */
    public function renderDataProvider(): array
    {
        return [
            'empty key, empty string value' => [
                'pair' => new ArrayPair(
                    new ArrayKey(''),
                    new LiteralExpression('\'\'')
                ),
                'expectedString' => "'' => '',",
            ],
            'empty key, string value' => [
                'pair' => new ArrayPair(
                    new ArrayKey(''),
                    new LiteralExpression('\'value\'')
                ),
                'expectedString' => "'' => 'value',",
            ],
            'empty key, integer value' => [
                'pair' => new ArrayPair(
                    new ArrayKey(''),
                    new LiteralExpression('2')
                ),
                'expectedString' => "'' => 2,",
            ],
            'string value' => [
                'pair' => new ArrayPair(
                    new ArrayKey('key'),
                    new LiteralExpression('\'value\'')
                ),
                'expectedString' => "'key' => 'value',",
            ],
            'array value, empty' => [
                'pair' => new ArrayPair(
                    new ArrayKey('key'),
                    new ArrayExpression([]),
                ),
                'expectedString' => "'key' => [],",
            ],
            'array value, non-empty' => [
                'pair' => new ArrayPair(
                    new ArrayKey('key'),
                    new ArrayExpression([
                        new ArrayPair(
                            new ArrayKey('sub-key-1'),
                            new LiteralExpression('\'sub value 1\'')
                        ),
                        new ArrayPair(
                            new ArrayKey('sub-key-2'),
                            new LiteralExpression('\'sub value 2\'')
                        ),
                        new ArrayPair(
                            new ArrayKey('sub-key-3'),
                            new LiteralExpression('\'sub value 3\'')
                        ),
                    ]),
                ),
                'expectedString' => "'key' => [\n" .
                    "    'sub-key-1' => 'sub value 1',\n" .
                    "    'sub-key-2' => 'sub value 2',\n" .
                    "    'sub-key-3' => 'sub value 3',\n" .
                    '],',
            ],
        ];
    }
}
