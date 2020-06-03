<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource;

use webignition\BasilCompilableSource\Metadata\MetadataInterface;

trait HasMetadataTrait
{
    private MetadataInterface $metadata;

    public function getMetadata(): MetadataInterface
    {
        return $this->metadata;
    }
}
