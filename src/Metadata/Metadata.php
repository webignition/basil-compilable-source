<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Metadata;

use webignition\BasilCompilableSource\Block\ClassDependencyCollection;
use webignition\BasilCompilableSource\VariablePlaceholderCollection;

class Metadata implements MetadataInterface
{
    public const KEY_CLASS_DEPENDENCIES = 'class-dependencies';
    public const KEY_VARIABLE_DEPENDENCIES = 'variable-dependencies';
    public const KEY_VARIABLE_EXPORTS = 'variable-exports';

    private ClassDependencyCollection $classDependencies;
    private VariablePlaceholderCollection $variableDependencies;
    private VariablePlaceholderCollection $variableExports;

    /**
     * @param array<mixed> $components
     */
    public function __construct(array $components = [])
    {
        $classDependencies = $components[self::KEY_CLASS_DEPENDENCIES] ?? new ClassDependencyCollection();
        $classDependencies = $classDependencies instanceof ClassDependencyCollection
            ? $classDependencies
            : new ClassDependencyCollection();

        $emptyVariableDependencies = VariablePlaceholderCollection::createDependencyCollection();
        $variableDependencies = $components[self::KEY_VARIABLE_DEPENDENCIES] ?? $emptyVariableDependencies;
        $variableDependencies = $variableDependencies instanceof VariablePlaceholderCollection
            ? $variableDependencies
            : $emptyVariableDependencies;

        $emptyVariableExports = VariablePlaceholderCollection::createExportCollection();
        $variableExports = $components[self::KEY_VARIABLE_EXPORTS] ?? $emptyVariableExports;
        $variableExports = $variableExports instanceof VariablePlaceholderCollection
            ? $variableExports
            : $emptyVariableExports;

        $this->classDependencies = $classDependencies;
        $this->variableDependencies = $variableDependencies;
        $this->variableExports = $variableExports;
    }

    public function getClassDependencies(): ClassDependencyCollection
    {
        return $this->classDependencies;
    }

    public function getVariableExports(): VariablePlaceholderCollection
    {
        return $this->variableExports;
    }

    public function getVariableDependencies(): VariablePlaceholderCollection
    {
        return $this->variableDependencies;
    }

    public function merge(MetadataInterface $metadata): MetadataInterface
    {
        $new = clone $this;
        $new->mergeClassDependencies($metadata->getClassDependencies());
        $new->variableDependencies->merge($metadata->getVariableDependencies());
        $new->variableExports->merge($metadata->getVariableExports());

        return $new;
    }

    private function mergeClassDependencies(ClassDependencyCollection $classDependencies): void
    {
        foreach ($classDependencies->getLines() as $classDependency) {
            if ($this->classDependencies->canLineBeAdded($classDependency)) {
                $this->classDependencies->addLine($classDependency);
            }
        }
    }
}
