<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Block;

use webignition\BasilCompilableSource\Metadata\MetadataInterface;

interface CodeBlockInterface extends BlockInterface
{
    public function getMetadata(): MetadataInterface;
}
