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

class ArrayExpression implements ExpressionInterface, RenderableInterface
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

    public static function fromDataSets(string $identifier, array $dataSets): self
    {
        $expressionArrayPairs = [];

        foreach ($dataSets as $dataSetName => $dataSet) {
            $dataSetArrayPairs = [];

            foreach ($dataSet as $key => $value) {
                $valueExpression = $value instanceof ExpressionInterface
                    ? $value
                    : new LiteralExpression('\'' . $value . '\'');

                $dataSetArrayPairs[] = new ArrayPair(
                    new ArrayKey($key),
                    $valueExpression
                );
            }

            $dataSetArrayFoo = new ArrayExpression(
                $identifier . '-' . $dataSetName . '-',
                $dataSetArrayPairs
            );

            $dataSetArrayPair = new ArrayPair(
                new ArrayKey((string) $dataSetName),
                $dataSetArrayFoo
            );

            $expressionArrayPairs[] = $dataSetArrayPair;
        }

        return new ArrayExpression($identifier, $expressionArrayPairs);
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
        $lines = explode("\n", $resolved);

        foreach ($lines as $lineIndex => $line) {
            if ($lineIndex > 0) {
                $lines[$lineIndex] = self::INDENT . $line;
            }
        }

        $resolved = implode("\n", $lines);

        return self::INDENT .  $resolved . "\n";
    }
}
