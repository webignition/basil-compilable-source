<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource;

use webignition\BasilCompilableSource\Block\ClassDependencyCollection;
use webignition\BasilCompilableSource\Metadata\Metadata;
use webignition\BasilCompilableSource\Metadata\MetadataInterface;

class ClassDefinition implements ClassDefinitionInterface
{
    private const RENDER_TEMPLATE = <<<'EOD'
%s

%s
{%s}
EOD;

    private ClassSignature $signature;

    /**
     * @var MethodDefinitionInterface[]
     */
    private array $methods = [];

    /**
     * @param ClassSignature $signature
     * @param MethodDefinitionInterface[] $methods
     */
    public function __construct(ClassSignature $signature, array $methods)
    {
        $this->signature = $signature;

        foreach ($methods as $method) {
            if ($method instanceof MethodDefinitionInterface) {
                $this->methods[$method->getName()] = $method;
            }
        }
    }

    public function getSignature(): ClassSignature
    {
        return $this->signature;
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
        $classDependencies = $this->getMetadata()->getClassDependencies();
        $baseClass = $this->signature->getBaseClass();

        if ($baseClass instanceof ClassName) {
            $classDependencies = $classDependencies->merge(new ClassDependencyCollection([
                $baseClass,
            ]));
        }

        return trim(sprintf(
            self::RENDER_TEMPLATE,
            $classDependencies->render(),
            $this->signature->render(),
            $this->createClassBody()
        ));
    }

    private function createClassBody(): string
    {
        if (0 === count($this->methods)) {
            return '';
        }

        $renderedMethods = [];

        foreach ($this->methods as $method) {
            $renderedMethod = $method->render();
            $renderedMethod = $this->indent($renderedMethod);

            $renderedMethods[] = $renderedMethod;
        }

        return "\n" . implode("\n\n", $renderedMethods) . "\n";
    }

    private function indent(string $content): string
    {
        $lines = explode("\n", $content);

        array_walk($lines, function (&$line) {
            if ('' !== $line) {
                $line = '    ' . $line;
            }
        });

        return implode("\n", $lines);
    }
}
