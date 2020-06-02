<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\TypeDeclaration;

use webignition\BasilCompilableSource\Metadata\MetadataInterface;
use webignition\BasilCompilableSource\SourceInterface;

interface TypeDeclarationInterface extends SourceInterface
{
    public function getMetadata(): MetadataInterface;
}
