<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Metadata;

use webignition\BasilCompilableSource\Block\ClassDependencyCollection;
use webignition\BasilCompilableSource\VariableDependencyCollection;

interface MetadataInterface
{
    public function getClassDependencies(): ClassDependencyCollection;

    public function getVariableDependencies(): VariableDependencyCollection;

    public function merge(MetadataInterface $metadata): MetadataInterface;
}
