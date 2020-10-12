<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Statement;

use webignition\BasilCompilableSource\Construct\ReturnConstruct;
use webignition\BasilCompilableSource\Expression\ExpressionInterface;
use webignition\BasilCompilableSource\Metadata\MetadataInterface;

class ReturnStatement implements StatementInterface
{
    private const RENDER_PATTERN = '%s %s';

    private Statement $statement;

    private function __construct(Statement $statement)
    {
        $this->statement = $statement;
    }

    public static function createFromExpression(ExpressionInterface $expression): self
    {
        $statement = new Statement($expression);

        return new ReturnStatement($statement);
    }

    public function getMetadata(): MetadataInterface
    {
        return $this->statement->getMetadata();
    }

    public function getExpression(): ExpressionInterface
    {
        return $this->statement->getExpression();
    }

    public function render(): string
    {
        return sprintf(
            self::RENDER_PATTERN,
            (new ReturnConstruct())->render(),
            $this->statement->render()
        );
    }
}
