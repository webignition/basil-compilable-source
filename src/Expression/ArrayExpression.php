<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Expression;

use webignition\BasilCompilableSource\DeferredResolvableCreationTrait;
use webignition\BasilCompilableSource\Metadata\Metadata;
use webignition\BasilCompilableSource\Metadata\MetadataInterface;
use webignition\StubbleResolvable\ResolvableCollection;
use webignition\StubbleResolvable\ResolvableInterface;
use webignition\StubbleResolvable\ResolvedTemplateMutationInterface;
use webignition\StubbleResolvable\ResolvedTemplateMutatorResolvable;

class ArrayExpression implements ExpressionInterface, ResolvedTemplateMutationInterface
{
    use DeferredResolvableCreationTrait;

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
            $expressionArrayPairs[] = new ArrayPair(
                new ArrayKey((string) $dataSetName),
                self::fromArray($dataSet)
            );
        }

        return new ArrayExpression($expressionArrayPairs);
    }

    public static function fromArray(array $array): self
    {
        $arrayPairs = [];

        foreach ($array as $key => $value) {
            $arrayPair = self::createArrayPair((string) $key, $value);
            if ($arrayPair instanceof ArrayPair) {
                $arrayPairs[] = $arrayPair;
            }
        }

        return new ArrayExpression($arrayPairs);
    }

    private static function createArrayPair(string $key, $value): ?ArrayPair
    {
        $valueExpression = self::createExpression($value);
        if ($valueExpression instanceof ExpressionInterface) {
            return new ArrayPair(
                new ArrayKey((string) $key),
                $valueExpression
            );
        }

        return null;
    }

    private static function createExpression($value): ?ExpressionInterface
    {
        if ($value instanceof ExpressionInterface) {
            return $value;
        }

        if (is_scalar($value)) {
            if (is_string($value)) {
                $value = '\'' . $value . '\'';
            }

            return new LiteralExpression((string) $value);
        }

        if (is_array($value)) {
            return self::fromArray($value);
        }

        return null;
    }

    public function getMetadata(): MetadataInterface
    {
        $metadata = new Metadata();
        array_walk($this->pairs, function (ArrayPair $pair) use (&$metadata) {
            $metadata = $metadata->merge($pair->getMetadata());
        });

        return $metadata;
    }

    public function getResolvedTemplateMutator(): callable
    {
        return function (string $resolvedTemplate) {
            $prefix = '[';
            $suffix = ']';

            if ('' !== $resolvedTemplate) {
                $prefix .= "\n";
            }

            return $prefix . $resolvedTemplate . $suffix;
        };
    }

    protected function createResolvable(): ResolvableInterface
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

        return ResolvableCollection::create($resolvablePairs);
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
