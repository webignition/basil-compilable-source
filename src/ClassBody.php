<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource;

use webignition\BasilCompilableSource\Metadata\Metadata;
use webignition\BasilCompilableSource\Metadata\MetadataInterface;

class ClassBody
{
    /**
     * @var MethodDefinitionInterface[]
     */
    private array $methods = [];

    /**
     * @param MethodDefinitionInterface[] $methods
     */
    public function __construct(array $methods)
    {
        foreach ($methods as $method) {
            if ($method instanceof MethodDefinitionInterface) {
                $this->methods[$method->getName()] = $method;
            }
        }
    }

    /**
     * @return MethodDefinitionInterface[]
     */
    public function getMethods(): array
    {
        return $this->methods;
    }

    public function getMetadata(): MetadataInterface
    {
        $metadata = new Metadata();

        foreach ($this->methods as $method) {
            if ($method instanceof MethodDefinitionInterface) {
                $metadata = $metadata->merge($method->getMetadata());
            }
        }

        return $metadata;
    }

    public function render(): string
    {
        if (0 === count($this->methods)) {
            return '';
        }

        $renderedMethods = [];

        foreach ($this->methods as $method) {
            $renderedMethods[] = $method->render();
        }

        return implode("\n\n", $renderedMethods);
    }
}
