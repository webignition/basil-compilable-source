<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Tests\Unit\Annotation;

use webignition\BasilCompilableSource\Annotation\ParameterAnnotation;
use webignition\BasilCompilableSource\Tests\Unit\AbstractResolvableTest;
use webignition\BasilCompilableSource\VariableName;

class ParameterAnnotationTest extends AbstractResolvableTest
{
    /**
     * @dataProvider renderDataProvider
     */
    public function testRender(ParameterAnnotation $annotation, string $expectedString): void
    {
        $this->assertRenderResolvable($expectedString, $annotation);
    }

    /**
     * @return array[]
     */
    public function renderDataProvider(): array
    {
        return [
            'default' => [
                'annotation' => new ParameterAnnotation('string', new VariableName('name')),
                'expectedString' => '@param string $name',
            ],
        ];
    }
}
