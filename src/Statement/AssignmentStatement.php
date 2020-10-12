<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Statement;

use webignition\BasilCompilableSource\Expression\ExpressionInterface;
use webignition\BasilCompilableSource\Metadata\MetadataInterface;

class AssignmentStatement implements AssignmentStatementInterface
{
    private const RENDER_PATTERN = '%s = %s';

    private ExpressionInterface $variableDependency;
    private Statement $valueStatement;

    private function __construct(ExpressionInterface $variableDependency, Statement $valueStatement)
    {
        $this->variableDependency = $variableDependency;
        $this->valueStatement = $valueStatement;
    }

    public static function createFromExpression(
        ExpressionInterface $variableDependency,
        ExpressionInterface $valueExpression
    ): AssignmentStatementInterface {
        return new AssignmentStatement(
            $variableDependency,
            new Statement($valueExpression)
        );
    }

    public function getVariableDependency(): ExpressionInterface
    {
        return $this->variableDependency;
    }

    public function getMetadata(): MetadataInterface
    {
        $metadata = $this->variableDependency->getMetadata();
        $metadata = $metadata->merge($this->valueStatement->getMetadata());

        return $metadata;
    }

    public function getExpression(): ExpressionInterface
    {
        return $this->valueStatement->getExpression();
    }

    public function render(): string
    {
        return sprintf(
            self::RENDER_PATTERN,
            $this->variableDependency->render(),
            $this->valueStatement->render()
        );
    }
}
