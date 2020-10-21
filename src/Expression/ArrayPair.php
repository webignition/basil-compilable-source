<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Expression;

use webignition\BasilCompilableSource\HasMetadataInterface;
use webignition\BasilCompilableSource\Metadata\MetadataInterface;
use webignition\BasilCompilableSource\RenderableInterface;
use webignition\BasilCompilableSource\RenderFromTemplateTrait;
use webignition\StubbleResolvable\Resolvable;
use webignition\StubbleResolvable\ResolvableInterface;
use webignition\StubbleResolvable\ResolvedTemplateMutatorTrait;

class ArrayPair implements RenderableInterface, ResolvableInterface, HasMetadataInterface
{
    use RenderFromTemplateTrait;
    use ResolvedTemplateMutatorTrait;

    private const RENDER_TEMPLATE = '{{ key }} => {{ value }},';

    private ArrayKey $key;

    /**
     * @var mixed
     */
    private $value;

    public function __construct(ArrayKey $key, ExpressionInterface $value)
    {
        $this->key = $key;
        $this->value = $value;
    }

    public function getResolvable(): ResolvableInterface
    {
        return new Resolvable(
            self::RENDER_TEMPLATE,
            [
                'key' => (string) $this->key,
                'value' => $this->value->render(),
            ]
        );
    }

    public function getTemplate(): string
    {
        return $this->getResolvable()->getTemplate();
    }

    public function getContext(): array
    {
        return $this->getResolvable()->getContext();
    }

    public function getMetadata(): MetadataInterface
    {
        return $this->value->getMetadata();
    }
}
