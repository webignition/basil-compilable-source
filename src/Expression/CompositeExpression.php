<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Expression;

use webignition\BasilCompilableSource\Metadata\Metadata;
use webignition\BasilCompilableSource\Metadata\MetadataInterface;
use webignition\BasilCompilableSource\RenderTrait;
use webignition\StubbleResolvable\ResolvableCollection;
use webignition\StubbleResolvable\ResolvableInterface;

class CompositeExpression implements ExpressionInterface
{
    use RenderTrait;

    /**
     * @var ExpressionInterface[]
     */
    private $expressions;

    private ?ResolvableInterface $resolvable = null;

    /**
     * @param array<mixed> $expressions
     */
    public function __construct(array $expressions)
    {
        $this->expressions = array_filter($expressions, function ($item) {
            return $item instanceof ExpressionInterface;
        });

        $metadata = new Metadata();
        foreach ($this->expressions as $expression) {
            $metadata = $metadata->merge($expression->getMetadata());
        }
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
        $metadata = new Metadata();
        foreach ($this->expressions as $expression) {
            $metadata = $metadata->merge($expression->getMetadata());
        }

        return $metadata;
    }

    private function getResolvable(): ResolvableInterface
    {
        if (null === $this->resolvable) {
            $this->resolvable = ResolvableCollection::create($this->expressions);
        }

        return $this->resolvable;
    }
}
