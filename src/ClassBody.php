<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource;

use webignition\BasilCompilableSource\Metadata\Metadata;
use webignition\BasilCompilableSource\Metadata\MetadataInterface;
use webignition\StubbleResolvable\ResolvableCollection;
use webignition\StubbleResolvable\ResolvableInterface;
use webignition\StubbleResolvable\ResolvedTemplateMutationInterface;
use webignition\StubbleResolvable\ResolvedTemplateMutatorResolvable;

class ClassBody implements ResolvableInterface, ResolvedTemplateMutationInterface
{
    use DeferredResolvableCreationTrait;
    use RenderTrait;

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

    public function getResolvedTemplateMutator(): callable
    {
        return function (string $resolvedTemplate): string {
            return rtrim($resolvedTemplate);
        };
    }

    protected function createResolvable(): ResolvableInterface
    {
        $resolvables = [];

        foreach ($this->methods as $method) {
            $resolvables[] = new ResolvedTemplateMutatorResolvable(
                $method,
                function (string $resolvedTemplate): string {
                    return $this->methodResolvedTemplateMutator($resolvedTemplate);
                }
            );
        }

        return ResolvableCollection::create($resolvables);
    }

    private function methodResolvedTemplateMutator(string $resolvedTemplate): string
    {
        return rtrim($resolvedTemplate) . "\n\n";
    }
}
