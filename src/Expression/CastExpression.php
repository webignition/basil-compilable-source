<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Expression;

use webignition\BasilCompilableSource\RenderFromTemplateTrait;

class CastExpression extends AbstractExpression
{
    use RenderFromTemplateTrait;

    private ExpressionInterface $expression;
    private string $castTo;

    public function __construct(ExpressionInterface $expression, string $castTo)
    {
        $this->expression = new EncapsulatedExpression($expression);
        $this->castTo = $castTo;

        parent::__construct($expression->getMetadata());
    }

    protected function getRenderTemplate(): string
    {
        return '({{ cast_type }}) {{ expression }}';
    }

    protected function getRenderContext(): array
    {
        return [
            'cast_type' => $this->castTo,
            'expression' => $this->expression->render(),
        ];
    }
}
