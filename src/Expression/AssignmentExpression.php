<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Expression;

use webignition\BasilCompilableSource\Metadata\MetadataInterface;
use webignition\BasilCompilableSource\RenderTrait;

class AssignmentExpression implements AssignmentExpressionInterface
{
    use RenderTrait;

    private const RENDER_TEMPLATE = '{{ variable }} {{ operator }} {{ value }}';

    public const OPERATOR_ASSIGMENT_EQUALS = '=';

    private ExpressionInterface $variable;
    private ExpressionInterface $value;
    private string $operator;

    public function __construct(
        ExpressionInterface $variable,
        ExpressionInterface $value,
        string $operator = self::OPERATOR_ASSIGMENT_EQUALS
    ) {
        $this->variable = $variable;
        $this->value = $value;
        $this->operator = $operator;
    }

    public function getVariable(): ExpressionInterface
    {
        return $this->variable;
    }

    public function getValue(): ExpressionInterface
    {
        return $this->value;
    }

    public function getOperator(): string
    {
        return $this->operator;
    }

    public function getMetadata(): MetadataInterface
    {
        $metadata = $this->variable->getMetadata();
        return $metadata->merge($this->value->getMetadata());
    }

    public function getTemplate(): string
    {
        return self::RENDER_TEMPLATE;
    }

    public function getContext(): array
    {
        return [
            'variable' => $this->variable,
            'operator' => $this->operator,
            'value' => $this->value,
        ];
    }
}
