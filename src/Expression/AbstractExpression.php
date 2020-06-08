<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Expression;

use webignition\BasilCompilableSource\HasMetadataTrait;
use webignition\BasilCompilableSource\Metadata\Metadata;
use webignition\BasilCompilableSource\Metadata\MetadataInterface;

abstract class AbstractExpression implements ExpressionInterface
{
    use HasMetadataTrait;

    public function __construct(MetadataInterface $metadata = null)
    {
        if (null === $metadata) {
            $metadata = new Metadata();
        }

        $this->metadata = $metadata;
    }
}
