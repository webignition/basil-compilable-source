<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Statement;

use webignition\BasilCompilableSource\Expression\ExpressionInterface;
use webignition\BasilCompilableSource\Metadata\MetadataInterface;

abstract class AbstractAssignmentStatement implements AssignmentStatementInterface
{
    private const RENDER_PATTERN = '%s = %s';

    private ExpressionInterface $variable;
    private Statement $valueStatement;

    protected function __construct(ExpressionInterface $variable, Statement $valueStatement)
    {
        $this->variable = $variable;
        $this->valueStatement = $valueStatement;
    }

    public function getMetadata(): MetadataInterface
    {
        $metadata = $this->getVariable()->getMetadata();
        return $metadata->merge($this->valueStatement->getMetadata());
    }

    public function getExpression(): ExpressionInterface
    {
        return $this->valueStatement->getExpression();
    }

    public function getVariable(): ExpressionInterface
    {
        return $this->variable;
    }

    public function render(): string
    {
        return sprintf(
            self::RENDER_PATTERN,
            $this->getVariable()->render(),
            $this->valueStatement->render()
        );
    }
}
