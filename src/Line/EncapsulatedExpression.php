<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Line;

class EncapsulatedExpression extends AbstractExpression
{
    private $expression;

    public function __construct(ExpressionInterface $expression)
    {
        $this->expression = $expression;

        parent::__construct($expression->getMetadata());
    }

    public function render(): string
    {
        return '(' . $this->expression->render() . ')';
    }
}
