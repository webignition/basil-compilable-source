<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Metadata;

use webignition\BasilCompilableSource\Block\ClassDependencyCollection;
use webignition\BasilCompilableSource\VariableDependencyCollection;

class Metadata implements MetadataInterface
{
    public const KEY_CLASS_DEPENDENCIES = 'class-dependencies';
    public const KEY_VARIABLE_DEPENDENCIES = 'variable-dependencies';
    public const KEY_VARIABLE_EXPORTS = 'variable-exports';

    private ClassDependencyCollection $classDependencies;
    private VariableDependencyCollection $variableDependencies;
    private VariableDependencyCollection $variableExports;

    /**
     * @param array<mixed> $components
     */
    public function __construct(array $components = [])
    {
        $classDependencies = $components[self::KEY_CLASS_DEPENDENCIES] ?? new ClassDependencyCollection();
        $classDependencies = $classDependencies instanceof ClassDependencyCollection
            ? $classDependencies
            : new ClassDependencyCollection();

        $emptyVariableDependencies = VariableDependencyCollection::createDependencyCollection();
        $variableDependencies = $components[self::KEY_VARIABLE_DEPENDENCIES] ?? $emptyVariableDependencies;
        $variableDependencies = $variableDependencies instanceof VariableDependencyCollection
            ? $variableDependencies
            : $emptyVariableDependencies;

        $emptyVariableExports = VariableDependencyCollection::createExportCollection();
        $variableExports = $components[self::KEY_VARIABLE_EXPORTS] ?? $emptyVariableExports;
        $variableExports = $variableExports instanceof VariableDependencyCollection
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

    public function getVariableExports(): VariableDependencyCollection
    {
        return $this->variableExports;
    }

    public function getVariableDependencies(): VariableDependencyCollection
    {
        return $this->variableDependencies;
    }

    public function merge(MetadataInterface $metadata): MetadataInterface
    {
        $new = new Metadata();
        $new->classDependencies = $this->classDependencies->merge($metadata->getClassDependencies());
        $new->variableDependencies = $this->variableDependencies->merge($metadata->getVariableDependencies());
        $new->variableExports = $this->variableExports->merge($metadata->getVariableExports());

        return $new;
    }
}
