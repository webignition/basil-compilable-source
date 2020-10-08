<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Statement;

use webignition\BasilCompilableSource\Body\BodyContentInterface;
use webignition\BasilCompilableSource\Body\BodyInterface;
use webignition\BasilCompilableSource\Construct\ReturnConstruct;
use webignition\BasilCompilableSource\Metadata\Metadata;
use webignition\BasilCompilableSource\Metadata\MetadataInterface;

class EmptyReturnStatement implements BodyContentInterface, BodyInterface
{
    private const RENDER_PATTERN = '%s;';

    public function getMetadata(): MetadataInterface
    {
        return new Metadata();
    }

    public function render(): string
    {
        return sprintf(
            self::RENDER_PATTERN,
            (new ReturnConstruct())->render()
        );
    }
}
