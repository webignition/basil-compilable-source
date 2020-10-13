<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Expression;

use webignition\BasilCompilableSource\RenderableInterface;
use webignition\BasilCompilableSource\RenderFromTemplateTrait;
use webignition\Stubble\Resolvable;
use webignition\Stubble\ResolvableInterface;

class EncapsulatedExpression extends AbstractExpression implements RenderableInterface
{
    use RenderFromTemplateTrait;

    private const RENDER_TEMPLATE = '({{ expression }})';

    private ExpressionInterface $expression;

    public function __construct(ExpressionInterface $expression)
    {
        $this->expression = $expression;

        parent::__construct($expression->getMetadata());
    }

    public function getResolvable(): ResolvableInterface
    {
        return new Resolvable(
            self::RENDER_TEMPLATE,
            [
                'expression' => $this->expression->render(),
            ]
        );
    }
}
