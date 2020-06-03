<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Metadata;

use webignition\BasilCompilableSource\Block\ClassDependencyCollection;
use webignition\BasilCompilableSource\ResolvablePlaceholderCollection;

interface MetadataInterface
{
    public function getClassDependencies(): ClassDependencyCollection;
    public function getVariableExports(): ResolvablePlaceholderCollection;
    public function getVariableDependencies(): ResolvablePlaceholderCollection;

    public function merge(MetadataInterface $metadata): MetadataInterface;
}
