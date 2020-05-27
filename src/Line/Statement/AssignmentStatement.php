<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Line\Statement;

use webignition\BasilCompilableSource\Line\ExpressionInterface;
use webignition\BasilCompilableSource\Metadata\MetadataInterface;

class AssignmentStatement extends Statement implements AssignmentStatementInterface
{
    private const RENDER_PATTERN = '%s = %s';

    private ExpressionInterface $placeholder;

    public function __construct(ExpressionInterface $placeholder, ExpressionInterface $expression)
    {
        parent::__construct($expression);

        $this->placeholder = $placeholder;
    }

    public function getVariablePlaceholder(): ExpressionInterface
    {
        return $this->placeholder;
    }

    public function getMetadata(): MetadataInterface
    {
        return parent::getMetadata()->merge($this->placeholder->getMetadata());
    }

    public function render(): string
    {
        return sprintf(
            self::RENDER_PATTERN,
            $this->placeholder->render(),
            parent::render()
        );
    }
}
