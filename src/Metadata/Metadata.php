<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Metadata;

use webignition\BasilCompilableSource\Block\ClassDependencyCollection;
use webignition\BasilCompilableSource\ResolvablePlaceholderCollection;

class Metadata implements MetadataInterface
{
    public const KEY_CLASS_DEPENDENCIES = 'class-dependencies';
    public const KEY_VARIABLE_DEPENDENCIES = 'variable-dependencies';
    public const KEY_VARIABLE_EXPORTS = 'variable-exports';

    private ClassDependencyCollection $classDependencies;
    private ResolvablePlaceholderCollection $variableDependencies;
    private ResolvablePlaceholderCollection $variableExports;

    /**
     * @param array<mixed> $components
     */
    public function __construct(array $components = [])
    {
        $classDependencies = $components[self::KEY_CLASS_DEPENDENCIES] ?? new ClassDependencyCollection();
        $classDependencies = $classDependencies instanceof ClassDependencyCollection
            ? $classDependencies
            : new ClassDependencyCollection();

        $emptyVariableDependencies = ResolvablePlaceholderCollection::createDependencyCollection();
        $variableDependencies = $components[self::KEY_VARIABLE_DEPENDENCIES] ?? $emptyVariableDependencies;
        $variableDependencies = $variableDependencies instanceof ResolvablePlaceholderCollection
            ? $variableDependencies
            : $emptyVariableDependencies;

        $emptyVariableExports = ResolvablePlaceholderCollection::createExportCollection();
        $variableExports = $components[self::KEY_VARIABLE_EXPORTS] ?? $emptyVariableExports;
        $variableExports = $variableExports instanceof ResolvablePlaceholderCollection
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

    public function getVariableExports(): ResolvablePlaceholderCollection
    {
        return $this->variableExports;
    }

    public function getVariableDependencies(): ResolvablePlaceholderCollection
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
