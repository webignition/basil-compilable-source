<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Statement;

use webignition\BasilCompilableSource\Expression\ExpressionInterface;
use webignition\BasilCompilableSource\Metadata\MetadataInterface;

class AssignmentStatement extends Statement implements AssignmentStatementInterface
{
    private const RENDER_PATTERN = '%s = %s';

    private ExpressionInterface $variableDependency;

    public function __construct(ExpressionInterface $variableDependency, ExpressionInterface $expression)
    {
        parent::__construct($expression);

        $this->variableDependency = $variableDependency;
    }

    public function getVariableDependency(): ExpressionInterface
    {
        return $this->variableDependency;
    }

    public function getMetadata(): MetadataInterface
    {
        return parent::getMetadata()->merge($this->variableDependency->getMetadata());
    }

    public function render(): string
    {
        return sprintf(
            self::RENDER_PATTERN,
            $this->variableDependency->render(),
            parent::render()
        );
    }
}
