<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Tests\Unit\Annotation;

use webignition\BasilCompilableSource\Annotation\DataProviderAnnotation;
use webignition\BasilCompilableSource\Tests\Unit\AbstractResolvableTest;

class DataProviderAnnotationTest extends AbstractResolvableTest
{
    /**
     * @dataProvider renderDataProvider
     */
    public function testRender(DataProviderAnnotation $annotation, string $expectedString)
    {
        $this->assertRenderResolvable($expectedString, $annotation);
    }

    public function renderDataProvider(): array
    {
        return [
            'default' => [
                'annotation' => new DataProviderAnnotation('dataProviderMethodName'),
                'expectedString' => '@dataProvider dataProviderMethodName',
            ],
        ];
    }
}
