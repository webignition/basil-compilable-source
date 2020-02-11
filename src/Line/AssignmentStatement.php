<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Line;

use webignition\BasilCompilableSource\VariablePlaceholder;

class AssignmentStatement extends Statement implements AssignmentStatementInterface
{
    private const RENDER_PATTERN = '%s = %s';

    private $placeholder;

    public function __construct(VariablePlaceholder $placeholder, ExpressionInterface $expression)
    {
        parent::__construct($expression);

        $this->placeholder = $placeholder;
    }

    public function getVariablePlaceholder(): VariablePlaceholder
    {
        return $this->placeholder;
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
