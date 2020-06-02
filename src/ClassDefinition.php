<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource;

use webignition\BasilCompilableSource\Block\ClassDependencyCollection;
use webignition\BasilCompilableSource\Line\ClassDependency;
use webignition\BasilCompilableSource\Metadata\Metadata;
use webignition\BasilCompilableSource\Metadata\MetadataInterface;

class ClassDefinition implements ClassDefinitionInterface
{
    private const RENDER_TEMPLATE = <<<'EOD'
%s

%s
{%s}
EOD;

    private const CLASS_SIGNATURE_TEMPLATE = 'class %s %s';
    private const NAMESPACE_SEPARATOR = '\\';

    private string $name;

    private ?ClassDependency $baseClass;

    /**
     * @var MethodDefinitionInterface[]
     */
    private array $methods = [];

    /**
     * @param string $name
     * @param MethodDefinitionInterface[] $methods
     */
    public function __construct(string $name, array $methods)
    {
        $this->name = $name;
        $this->baseClass = null;

        foreach ($methods as $method) {
            if ($method instanceof MethodDefinitionInterface) {
                $this->methods[$method->getName()] = $method;
            }
        }
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getBaseClass(): ?ClassDependency
    {
        return $this->baseClass;
    }

    public function setBaseClass(ClassDependency $baseClass): void
    {
        $this->baseClass = $baseClass;
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
                $metadata->merge($method->getMetadata());
            }
        }

        return $metadata;
    }

    public function render(): string
    {
        $classDependencies = $this->getMetadata()->getClassDependencies();

        if ($this->baseClass instanceof ClassDependency) {
            $baseClassIsInRootNamespace = substr_count(
                $this->baseClass->getClassName(),
                self::NAMESPACE_SEPARATOR
            ) === 0;

            if (false === $baseClassIsInRootNamespace) {
                $classDependencies->merge(new ClassDependencyCollection([
                    $this->baseClass,
                ]));
            }
        }

        return trim(sprintf(
            self::RENDER_TEMPLATE,
            $classDependencies->render(),
            $this->createClassSignatureLine(),
            $this->createClassBody()
        ));
    }

    private function createClassSignatureLine(): string
    {
        $extendsSegment = '';

        if ($this->baseClass instanceof ClassDependency) {
            $extendsSegment = 'extends ' . $this->baseClass->getClass();
        }

        return trim(sprintf(self::CLASS_SIGNATURE_TEMPLATE, $this->getName(), $extendsSegment));
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
