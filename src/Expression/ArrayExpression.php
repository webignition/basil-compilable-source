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

    /**
     * @var ArrayPair[]
     */
    private array $pairs;

    /**
     * @param ArrayPair[] $pairs
     */
    public function __construct(array $pairs)
    {
        $pairs = array_filter($pairs, function ($item) {
            return $item instanceof ArrayPair;
        });

        $this->pairs = $pairs;
    }

    /**
     * @param array<string|int, array<string, string|int|ExpressionInterface>> $dataSets
     *
     * @return self
     */
    public static function fromDataSets(array $dataSets): self
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

            $dataSetArrayExpression = new ArrayExpression($dataSetArrayPairs);

            $dataSetArrayPair = new ArrayPair(
                new ArrayKey((string) $dataSetName),
                $dataSetArrayExpression
            );

            $expressionArrayPairs[] = $dataSetArrayPair;
        }

        return new ArrayExpression($expressionArrayPairs);
    }

    public function getResolvable(): ResolvableInterface
    {
        $resolvablePairs = [];

        foreach ($this->pairs as $pair) {
            $resolvablePairs[] = new ResolvedTemplateMutatorResolvable(
                $pair,
                function (string $resolvedTemplate) {
                    return $this->arrayPairResolvedTemplateMutator($resolvedTemplate);
                }
            );
        }

        return new ResolvedTemplateMutatorResolvable(
            ResolvableCollection::create($resolvablePairs),
            function (string $resolved) {
                return $this->resolvedTemplateMutator($resolved);
            }
        );
    }

    public function getMetadata(): MetadataInterface
    {
        $metadata = new Metadata();
        array_walk($this->pairs, function (ArrayPair $pair) use (&$metadata) {
            $metadata = $metadata->merge($pair->getMetadata());
        });

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
