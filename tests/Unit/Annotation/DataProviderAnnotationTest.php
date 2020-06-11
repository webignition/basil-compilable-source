<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Tests\Unit\Annotation;

use webignition\BasilCompilableSource\Annotation\DataProviderAnnotation;

class DataProviderAnnotationTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider renderDataProvider
     */
    public function testRender(DataProviderAnnotation $annotation, string $expectedString)
    {
        $this->assertSame($expectedString, $annotation->render());
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
