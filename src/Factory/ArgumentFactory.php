<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Factory;

use webignition\BasilCompilableSource\Expression\ExpressionInterface;
use webignition\BasilCompilableSource\Expression\LiteralExpression;

class ArgumentFactory
{
    private SingleQuotedStringEscaper $singleQuotedStringEscaper;

    public function __construct(SingleQuotedStringEscaper $singleQuotedStringEscaper)
    {
        $this->singleQuotedStringEscaper = $singleQuotedStringEscaper;
    }

    public static function createFactory(): self
    {
        return new ArgumentFactory(
            SingleQuotedStringEscaper::create()
        );
    }

    /**
     * @param array<int|float|string|bool|ExpressionInterface> $arguments
     *
     * @return ExpressionInterface[]
     */
    public function create(array $arguments): array
    {
        $expressionArguments = [];

        foreach ($arguments as $argument) {
            if (is_scalar($argument)) {
                $argument = $this->createExpressionFromScalar($argument);
            }

            if ($argument instanceof ExpressionInterface) {
                $expressionArguments[] = $argument;
            }
        }

        return $expressionArguments;
    }

    /**
     * @param int|float|string|bool $scalar
     * @return ExpressionInterface
     */
    private function createExpressionFromScalar($scalar): ExpressionInterface
    {
        $expressionValue = '';

        if (is_int($scalar) || is_float($scalar)) {
            $expressionValue = (string) $scalar;
        }

        if (is_string($scalar)) {
            $escapedValue = $this->singleQuotedStringEscaper->escape($scalar);
            $expressionValue = '\'' . $escapedValue . '\'';
        }

        if (is_bool($scalar)) {
            $expressionValue = $scalar ? 'true' : 'false';
        }

        return new LiteralExpression($expressionValue);
    }
}