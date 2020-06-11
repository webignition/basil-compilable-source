<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Tests\Unit\DocBlock;

use webignition\BasilCompilableSource\DocBlock\ParameterAnnotation;
use webignition\BasilCompilableSource\VariableName;

class ParameterAnnotationTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider renderDataProvider
     */
    public function testRender(ParameterAnnotation $annotation, string $expectedString)
    {
        $this->assertSame($expectedString, $annotation->render());
    }

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
