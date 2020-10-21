<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Expression;

use webignition\BasilCompilableSource\Metadata\Metadata;
use webignition\BasilCompilableSource\Metadata\MetadataInterface;
use webignition\BasilCompilableSource\RenderableInterface;
use webignition\BasilCompilableSource\RenderFromTemplateTrait;
use webignition\StubbleResolvable\Resolvable;
use webignition\StubbleResolvable\ResolvableCollection;
use webignition\StubbleResolvable\ResolvableInterface;

class ArrayFoo implements ExpressionInterface, RenderableInterface
{
    use RenderFromTemplateTrait;

    private const INDENT = '    ';
    private ResolvableCollection $collection;

    /**
     * @param string $identifier
     * @param ArrayPair[] $pairs
     */
    public function __construct(string $identifier, array $pairs)
    {
        $pairs = array_filter($pairs, function ($item) {
            return $item instanceof ArrayPair;
        });

        array_walk($pairs, function (ArrayPair &$pair) {
            $pair = $pair->withResolvedTemplateMutator(function (string $resolved) {
                return $this->arrayPairResolvedTemplateMutator($resolved);
            });
        });

        $this->collection = new ResolvableCollection($identifier, $pairs);
    }

    public function getResolvable(): ResolvableInterface
    {
        $resolvable = new Resolvable(
            $this->collection->getTemplate(),
            $this->collection->getContext()
        );

        $resolvable = $resolvable->withResolvedTemplateMutator(function (string $resolved) {
            return $this->resolvedTemplateMutator($resolved);
        });

        return $resolvable;
    }

    public function getMetadata(): MetadataInterface
    {
        $metadata = new Metadata();

        foreach ($this->collection as $pair) {
            if ($pair instanceof ArrayPair) {
                $metadata = $metadata->merge($pair->getMetadata());
            }
        }

        return $metadata;
    }

    private function resolvedTemplateMutator(string $resolved): string
    {
        $prefix = '[';
        $suffix = ']';

        if ('' !== $resolved) {
            $prefix .= "\n";
        }

        return $prefix . $resolved . $suffix;
    }

    private function arrayPairResolvedTemplateMutator(string $resolved): string
    {
        return self::INDENT .  $resolved . "\n";
    }
}
