<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Statement;

use webignition\BasilCompilableSource\Expression\ExpressionInterface;
use webignition\BasilCompilableSource\Metadata\MetadataInterface;

abstract class AbstractAssignmentStatement implements AssignmentStatementInterface
{
    private const RENDER_PATTERN = '%s = %s';

    private Statement $valueStatement;

    protected function __construct(Statement $valueStatement)
    {
        $this->valueStatement = $valueStatement;
    }

    public function getMetadata(): MetadataInterface
    {
        $metadata = $this->getVariableDependency()->getMetadata();
        return $metadata->merge($this->valueStatement->getMetadata());
    }

    public function getExpression(): ExpressionInterface
    {
        return $this->valueStatement->getExpression();
    }

    public function render(): string
    {
        return sprintf(
            self::RENDER_PATTERN,
            $this->getVariableDependency()->render(),
            $this->valueStatement->render()
        );
    }
}
