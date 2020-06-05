<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Metadata;

use webignition\BasilCompilableSource\Block\ClassDependencyCollection;
use webignition\BasilCompilableSource\VariablePlaceholderCollection;

interface MetadataInterface
{
    public function getClassDependencies(): ClassDependencyCollection;
    public function getVariableExports(): VariablePlaceholderCollection;
    public function getVariableDependencies(): VariablePlaceholderCollection;

    public function merge(MetadataInterface $metadata): MetadataInterface;
}
