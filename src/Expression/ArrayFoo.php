<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Expression;

use webignition\BasilCompilableSource\RenderableInterface;
use webignition\BasilCompilableSource\RenderFromTemplateTrait;
use webignition\StubbleResolvable\Resolvable;
use webignition\StubbleResolvable\ResolvableCollection;
use webignition\StubbleResolvable\ResolvableInterface;

class ArrayFoo implements RenderableInterface
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

//    private const RENDER_TEMPLATE = '{{ key }} => {{ value }},';
//
//    private ArrayKey $key;
//
//    /**
//     * @var mixed
//     */
//    private $value;
//
//    public function __construct(ArrayKey $key, ExpressionInterface $value)
//    {
//        $this->key = $key;
//        $this->value = $value;
//    }
//
//    public function getResolvable(): ResolvableInterface
//    {
//        return new Resolvable(
//            self::RENDER_TEMPLATE,
//            [
//                'key' => $this->key->render(),
//                'value' => $this->value->render(),
//            ]
//        );
//    }

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
