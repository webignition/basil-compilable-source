<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Expression;

use webignition\BasilCompilableSource\RenderTrait;
use webignition\StubbleResolvable\Resolvable;
use webignition\StubbleResolvable\ResolvableInterface;
use webignition\StubbleResolvable\ResolvableProviderInterface;

class CastExpression extends AbstractExpression implements ResolvableProviderInterface
{
    use RenderTrait;

    private const RENDER_TEMPLATE = '({{ cast_type }}) {{ expression }}';

    private ExpressionInterface $expression;
    private string $castTo;

    public function __construct(ExpressionInterface $expression, string $castTo)
    {
        $this->expression = new EncapsulatedExpression($expression);
        $this->castTo = $castTo;

        parent::__construct($expression->getMetadata());
    }

    public function getResolvable(): ResolvableInterface
    {
        return new Resolvable(
            self::RENDER_TEMPLATE,
            [
                'cast_type' => $this->castTo,
                'expression' => $this->expression->render(),
            ]
        );
    }
}
