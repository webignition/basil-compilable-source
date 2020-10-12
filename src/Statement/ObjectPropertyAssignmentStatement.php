<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Statement;

use webignition\BasilCompilableSource\Expression\ExpressionInterface;
use webignition\BasilCompilableSource\Expression\ObjectPropertyAccessExpression;
use webignition\BasilCompilableSource\Metadata\MetadataInterface;

class ObjectPropertyAssignmentStatement implements AssignmentStatementInterface
{
    private const RENDER_PATTERN = '%s = %s';

    private ObjectPropertyAccessExpression $accessExpression;
    private Statement $valueStatement;

    private function __construct(ObjectPropertyAccessExpression $accessExpression, Statement $valueStatement)
    {
        $this->accessExpression = $accessExpression;
        $this->valueStatement = $valueStatement;
    }

    public static function createFromExpression(
        ObjectPropertyAccessExpression $accessExpression,
        ExpressionInterface $valueExpression
    ): self {
        return new ObjectPropertyAssignmentStatement(
            $accessExpression,
            new Statement($valueExpression)
        );
    }

    public function getVariableDependency(): ExpressionInterface
    {
        return $this->accessExpression;
    }

    public function getMetadata(): MetadataInterface
    {
        $metadata = $this->getVariableDependency()->getMetadata();
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
            $this->getVariableDependency()->render(),
            $this->valueStatement->render()
        );
    }
}
