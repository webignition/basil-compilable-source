<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Expression;

use webignition\BasilCompilableSource\HasMetadataInterface;
use webignition\BasilCompilableSource\Metadata\MetadataInterface;
use webignition\BasilCompilableSource\RenderTrait;
use webignition\StubbleResolvable\ResolvableInterface;

class ArrayPair implements ResolvableInterface, HasMetadataInterface
{
    use RenderTrait;

    private const RENDER_TEMPLATE = '{{ key }} => {{ value }},';

    private ArrayKey $key;
    private ExpressionInterface $value;

    public function __construct(ArrayKey $key, ExpressionInterface $value)
    {
        $this->key = $key;
        $this->value = $value;
    }

    public function getTemplate(): string
    {
        return self::RENDER_TEMPLATE;
    }

    public function getContext(): array
    {
        return [
            'key' => (string) $this->key,
            'value' => $this->value,
        ];
    }

    public function getMetadata(): MetadataInterface
    {
        return $this->value->getMetadata();
    }
}
