<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Line;

use webignition\BasilCompilableSource\Metadata\Metadata;

class CompositeExpression extends AbstractExpression
{
    /**
     * @var ExpressionInterface[]
     */
    private $expressions;

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

        parent::__construct($metadata);
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
