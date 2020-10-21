<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Tests\Unit\Expression;

use webignition\BasilCompilableSource\Expression\ArrayKey;
use webignition\BasilCompilableSource\Expression\ArrayPair;
use webignition\BasilCompilableSource\Expression\LiteralExpression;
use webignition\BasilCompilableSource\Metadata\Metadata;
use webignition\BasilCompilableSource\Metadata\MetadataInterface;
use webignition\BasilCompilableSource\MethodInvocation\ObjectMethodInvocation;
use webignition\BasilCompilableSource\VariableDependency;
use webignition\BasilCompilableSource\VariableDependencyCollection;

class ArrayPairTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider getMetadataDataProvider
     */
    public function testGetMetadata(ArrayPair $pair, MetadataInterface $expectedMetadata)
    {
        self::assertEquals($expectedMetadata, $pair->getMetadata());
    }

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
    public function testRender(ArrayPair $pair, string $expectedString)
    {
        $this->assertSame($expectedString, $pair->render());
    }

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
        ];
    }
}
