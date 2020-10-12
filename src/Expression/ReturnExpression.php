<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Expression;

use webignition\BasilCompilableSource\Construct\ReturnConstruct;
use webignition\BasilCompilableSource\Metadata\Metadata;
use webignition\BasilCompilableSource\Metadata\MetadataInterface;

class ReturnExpression implements ExpressionInterface
{
    private const RENDER_TEMPLATE = '%s%s';

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

    public function render(): string
    {
        $expressionContent = '';
        if ($this->expression instanceof ExpressionInterface) {
            $expressionContent = ' ' . $this->expression->render();
        }

        return sprintf(
            self::RENDER_TEMPLATE,
            (new ReturnConstruct())->render(),
            $expressionContent
        );
    }
}
