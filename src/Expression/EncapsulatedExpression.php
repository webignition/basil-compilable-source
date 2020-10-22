<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Expression;

use webignition\BasilCompilableSource\RenderTrait;
use webignition\StubbleResolvable\Resolvable;
use webignition\StubbleResolvable\ResolvableInterface;
use webignition\StubbleResolvable\ResolvableProviderInterface;

class EncapsulatedExpression extends AbstractExpression implements ResolvableProviderInterface
{
    use RenderTrait;

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
