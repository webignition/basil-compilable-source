<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Statement;

use webignition\BasilCompilableSource\Expression\ExpressionInterface;
use webignition\BasilCompilableSource\Metadata\MetadataInterface;
use webignition\BasilCompilableSource\RenderableInterface;
use webignition\BasilCompilableSource\RenderFromTemplateTrait;
use webignition\StubbleResolvable\Resolvable;
use webignition\StubbleResolvable\ResolvableInterface;

class Statement implements RenderableInterface, StatementInterface
{
    use RenderFromTemplateTrait;

    private const RENDER_TEMPLATE = '{{ expression }};';

    private ExpressionInterface $expression;

    public function __construct(ExpressionInterface $expression)
    {
        $this->expression = $expression;
    }

    public function getExpression(): ExpressionInterface
    {
        return $this->expression;
    }

    public function getMetadata(): MetadataInterface
    {
        return $this->expression->getMetadata();
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

    protected function getRenderTemplate(): string
    {
        return self::RENDER_TEMPLATE;
    }
}
