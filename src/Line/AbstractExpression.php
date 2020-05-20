<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Line;

use webignition\BasilCompilableSource\Metadata\Metadata;
use webignition\BasilCompilableSource\Metadata\MetadataInterface;

abstract class AbstractExpression implements ExpressionInterface
{
    private $metadata;

    public function __construct(?MetadataInterface $metadata = null)
    {
        $this->metadata = $metadata instanceof MetadataInterface ? $metadata : new Metadata();
    }

    public function getMetadata(): MetadataInterface
    {
        return $this->metadata;
    }
}
