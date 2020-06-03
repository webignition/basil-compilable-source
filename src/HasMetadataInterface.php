<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource;

use webignition\BasilCompilableSource\Metadata\MetadataInterface;

interface HasMetadataInterface
{
    public function getMetadata(): MetadataInterface;
}
