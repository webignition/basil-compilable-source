<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Expression;

use webignition\BasilCompilableSource\RenderableInterface;
use webignition\BasilCompilableSource\RenderFromTemplateTrait;
use webignition\BasilCompilableSource\RenderSource;
use webignition\BasilCompilableSource\RenderSourceInterface;

class CastExpression extends AbstractExpression implements RenderableInterface
{
    use RenderFromTemplateTrait;

    private const RENDER_TEMPLATE = '({{ cast_type }}) {{ expression }}';

    private ExpressionInterface $expression;
    private string $castTo;

    public function __construct(ExpressionInterface $expression, string $castTo)
    {
        $this->expression = new EncapsulatedExpression($expression);
        $this->castTo = $castTo;

        parent::__construct($expression->getMetadata());
    }

    public function getRenderSource(): RenderSourceInterface
    {
        return new RenderSource(
            self::RENDER_TEMPLATE,
            [
                'cast_type' => $this->castTo,
                'expression' => $this->expression->render(),
            ]
        );
    }
}
