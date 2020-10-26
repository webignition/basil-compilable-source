<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\TypeDeclaration;

use webignition\BasilCompilableSource\Metadata\MetadataInterface;
use webignition\StubbleResolvable\ResolvableInterface;

interface TypeDeclarationInterface extends ResolvableInterface
{
    public function getMetadata(): MetadataInterface;
}
