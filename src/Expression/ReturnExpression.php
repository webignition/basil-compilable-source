<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Expression;

use webignition\BasilCompilableSource\Construct\ReturnConstruct;
use webignition\BasilCompilableSource\Metadata\Metadata;
use webignition\BasilCompilableSource\Metadata\MetadataInterface;
use webignition\BasilCompilableSource\RenderTrait;
use webignition\StubbleResolvable\Resolvable;
use webignition\StubbleResolvable\ResolvableInterface;
use webignition\StubbleResolvable\ResolvableProviderInterface;

class ReturnExpression implements ExpressionInterface, ResolvableProviderInterface
{
    use RenderTrait;

    private const RENDER_TEMPLATE = '{{ return_construct }}{{ expression_content }}';

    private ?ExpressionInterface $expression;

    public function __construct(?ExpressionInterface $expression = null)
    {
        $this->expression = $expression;
    }

    public function getMetadata(): MetadataInterface
    {
        return $this->expression instanceof ExpressionInterface
            ? $this->expression->getMetadata()
            : new Metadata();
    }

    public function getResolvable(): ResolvableInterface
    {
        return new Resolvable(
            self::RENDER_TEMPLATE,
            [
                'return_construct' => new ReturnConstruct(),
                'expression_content' => $this->expression instanceof ExpressionInterface
                    ? ' ' . $this->expression->render()
                    : ''
            ]
        );
    }
}
