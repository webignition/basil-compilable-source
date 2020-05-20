<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Line;

class CastExpression extends AbstractExpression
{
    private $expression;
    private $castTo;

    public function __construct(ExpressionInterface $expression, string $castTo)
    {
        $this->expression = new EncapsulatedExpression($expression);
        $this->castTo = $castTo;

        parent::__construct($expression->getMetadata());
    }

    public function render(): string
    {
        return '(' . $this->castTo . ') ' . $this->expression->render();
    }
}
