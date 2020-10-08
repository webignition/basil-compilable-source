<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Construct;

use webignition\BasilCompilableSource\Metadata\Metadata;
use webignition\BasilCompilableSource\Metadata\MetadataInterface;
use webignition\BasilCompilableSource\SourceInterface;

class ReturnConstruct implements SourceInterface
{
    public function render(): string
    {
        return 'return';
    }

    public function getMetadata(): MetadataInterface
    {
        return new Metadata();
    }
}
