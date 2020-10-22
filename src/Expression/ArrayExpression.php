<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Expression;

use webignition\BasilCompilableSource\Metadata\Metadata;
use webignition\BasilCompilableSource\Metadata\MetadataInterface;
use webignition\BasilCompilableSource\RenderTrait;
use webignition\StubbleResolvable\ResolvableCollection;
use webignition\StubbleResolvable\ResolvableInterface;
use webignition\StubbleResolvable\ResolvableProviderInterface;
use webignition\StubbleResolvable\ResolvedTemplateMutatorResolvable;

class ArrayExpression implements ExpressionInterface, ResolvableProviderInterface
{
    use RenderTrait;

    private const INDENT = '    ';
    private const IDENTIFIER_PREFIX = 'array-expression-';

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
            $pair = new ResolvedTemplateMutatorResolvable(
                $pair,
                function (string $resolvedTemplate) {
                    return $this->arrayPairResolvedTemplateMutator($resolvedTemplate);
                }
            );
        });

        $this->collection = new ResolvableCollection(self::IDENTIFIER_PREFIX . $identifier, $pairs);
    }

    /**
     * @param string $identifier
     * @param array<string|int, array<string, string|int|ExpressionInterface>> $dataSets
     *
     * @return self
     */
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

            $dataSetArrayExpression = new ArrayExpression(
                $identifier . '-' . $dataSetName . '-',
                $dataSetArrayPairs
            );

            $dataSetArrayPair = new ArrayPair(
                new ArrayKey((string) $dataSetName),
                $dataSetArrayExpression
            );

            $expressionArrayPairs[] = $dataSetArrayPair;
        }

        return new ArrayExpression($identifier, $expressionArrayPairs);
    }

    public function getResolvable(): ResolvableInterface
    {
        return new ResolvedTemplateMutatorResolvable(
            $this->collection,
            function (string $resolved) {
                return $this->resolvedTemplateMutator($resolved);
            }
        );
    }

    public function getMetadata(): MetadataInterface
    {
        $metadata = new Metadata();

        foreach ($this->collection as $pair) {
            if ($pair instanceof ResolvableProviderInterface) {
                $pair = $pair->getResolvable();
            }

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

        return self::INDENT . $resolved . "\n";
    }
}
