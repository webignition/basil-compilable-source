<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Line;

use webignition\BasilCompilableSource\Metadata\Metadata;
use webignition\BasilCompilableSource\Metadata\MetadataInterface;

class CompositeExpression implements ExpressionInterface
{
    /**
     * @var ExpressionInterface[]
     */
    private $expressions;

    /**
     * @var MetadataInterface
     */
    private $metadata;

    /**
     * @param array<mixed> $expressions
     */
    public function __construct(array $expressions)
    {
        $this->expressions = array_filter($expressions, function ($item) {
            return $item instanceof ExpressionInterface;
        });

        $this->metadata = new Metadata();
        foreach ($this->expressions as $expression) {
            $this->metadata = $this->metadata->merge($expression->getMetadata());
        }
    }

    public function getMetadata(): MetadataInterface
    {
        return $this->metadata;
    }

    public function render(): string
    {
        return (string) array_reduce(
            $this->expressions,
            function (?string $content, ExpressionInterface $expression) {
                return $content . $expression->render();
            }
        );
    }
}
