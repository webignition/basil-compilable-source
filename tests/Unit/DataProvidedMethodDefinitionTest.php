<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Tests\Unit;

use webignition\BasilCompilableSource\Body\Body;
use webignition\BasilCompilableSource\DataProvidedMethodDefinition;
use webignition\BasilCompilableSource\DataProviderMethodDefinition;
use webignition\BasilCompilableSource\DataProviderMethodDefinitionInterface;
use webignition\BasilCompilableSource\MethodDefinition;
use webignition\BasilCompilableSource\MethodDefinitionInterface;

class DataProvidedMethodDefinitionTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider createDataProvider
     */
    public function testCreate(
        MethodDefinitionInterface $methodDefinition,
        DataProviderMethodDefinitionInterface $dataProviderMethodDefinition
    ) {
        $dataProvidedMethodDefinition = new DataProvidedMethodDefinition(
            $methodDefinition,
            $dataProviderMethodDefinition
        );

        $this->assertSame($methodDefinition->getMetadata(), $dataProvidedMethodDefinition->getMetadata());
        $this->assertSame($methodDefinition->getArguments(), $dataProvidedMethodDefinition->getArguments());
        $this->assertSame($methodDefinition->getName(), $dataProvidedMethodDefinition->getName());
        $this->assertSame($methodDefinition->getReturnType(), $dataProvidedMethodDefinition->getReturnType());
        $this->assertSame($methodDefinition->getVisibility(), $dataProvidedMethodDefinition->getVisibility());
        $this->assertSame($methodDefinition->isStatic(), $dataProvidedMethodDefinition->isStatic());
    }

    public function createDataProvider(): array
    {
        return [
            'default' => [
                'methodDefinition' => new MethodDefinition('methodName', new Body([]), []),
                'dataProviderMethodDefinition' => new DataProviderMethodDefinition('dataProviderMethodName', []),
            ],
        ];
    }

    /**
     * @dataProvider renderDataProvider
     */
    public function testRender(DataProvidedMethodDefinition $definition, string $expectedString)
    {
        $this->assertSame($expectedString, $definition->render());
    }

    public function renderDataProvider(): array
    {
        return [
            'default' => [
                'definition' => new DataProvidedMethodDefinition(
                    new MethodDefinition(
                        'methodName',
                        new Body([]),
                        [
                            'alpha',
                            'charlie',
                        ]
                    ),
                    new DataProviderMethodDefinition(
                        'dataProviderMethodName',
                        [
                            0 => [
                                'alpha' => 'value1',
                                'charlie' => 'value1',
                            ],
                        ]
                    )
                ),
                'expectedString' =>
                    '/**' . "\n" .
                    ' * @dataProvider dataProviderMethodName' . "\n" .
                    ' *' . "\n" .
                    ' * @param string $alpha' . "\n" .
                    ' * @param string $charlie' . "\n" .
                    ' */' . "\n" .
                    'public function methodName($alpha, $charlie)' . "\n" .
                    '{' . "\n" .
                    "\n" .
                    '}' . "\n" .
                    "\n" .
                    'public function dataProviderMethodName(): array' . "\n" .
                    '{' . "\n" .
                    '    return [' . "\n" .
                    '        \'0\' => [' . "\n" .
                    '            \'alpha\' => \'value1\',' . "\n" .
                    '            \'charlie\' => \'value1\',' . "\n" .
                    '        ],' . "\n" .
                    '    ];' . "\n" .
                    '}',
            ],
        ];
    }
}
