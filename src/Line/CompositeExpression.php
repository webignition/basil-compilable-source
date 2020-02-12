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
     * @param string|null $castTo
     */
    public function __construct(array $expressions, ?string $castTo = null)
    {
        $this->expressions = array_filter($expressions, function ($item) {
            return $item instanceof ExpressionInterface;
        });

        $metadata = new Metadata();
        foreach ($this->expressions as $expression) {
            $metadata = $metadata->merge($expression->getMetadata());
        }

        parent::__construct($castTo, $metadata);
    }

    public function render(): string
    {
        return parent::render() . (string) array_reduce(
            $this->expressions,
            function (?string $content, ExpressionInterface $expression) {
                return $content . $expression->render();
            }
        );
    }
}
