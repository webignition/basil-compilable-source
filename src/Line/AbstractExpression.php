<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Line;

use webignition\BasilCompilableSource\Metadata\Metadata;
use webignition\BasilCompilableSource\Metadata\MetadataInterface;

abstract class AbstractExpression implements ExpressionInterface
{
    private $metadata;
    private $castTo;

    public function __construct(?string $castTo = null, ?MetadataInterface $metadata = null)
    {
        $this->castTo = $castTo;
        $this->metadata = $metadata instanceof MetadataInterface ? $metadata : new Metadata();
    }

    public function getMetadata(): MetadataInterface
    {
        return $this->metadata;
    }

    public function getCastTo(): ?string
    {
        return $this->castTo;
    }

    public function render(): string
    {
        return null === $this->castTo
            ? ''
            : '(' . $this->castTo . ') ';
    }
}
