<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Line;

class EncapsulatedExpression extends AbstractExpression
{
    private $expression;

    public function __construct(ExpressionInterface $expression, ?string $castTo = null)
    {
        $this->expression = $expression;

        parent::__construct($castTo, $expression->getMetadata());
    }

    public function render(): string
    {
        return parent::render() . '(' . $this->expression->render() . ')';
    }
}
