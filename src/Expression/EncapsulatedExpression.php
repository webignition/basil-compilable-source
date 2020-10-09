<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Expression;

use webignition\BasilCompilableSource\RenderFromTemplateTrait;

class EncapsulatedExpression extends AbstractExpression
{
    use RenderFromTemplateTrait;

    private const RENDER_TEMPLATE = '({{ expression }})';

    private ExpressionInterface $expression;

    public function __construct(ExpressionInterface $expression)
    {
        $this->expression = $expression;

        parent::__construct($expression->getMetadata());
    }

    protected function getRenderTemplate(): string
    {
        return self::RENDER_TEMPLATE;
    }

    protected function getRenderContext(): array
    {
        return [
            'expression' => $this->expression->render(),
        ];
    }
}
