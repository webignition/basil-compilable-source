<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Line;

use webignition\BasilCompilableSource\Metadata\MetadataInterface;

class Statement implements StatementInterface
{
    private const RENDER_PATTERN = '%s;';

    private $expression;

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

    public function render(): string
    {
        return sprintf(
            self::RENDER_PATTERN,
            $this->expression->render()
        );
    }
}
