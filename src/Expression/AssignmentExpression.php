<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Expression;

use webignition\BasilCompilableSource\Metadata\MetadataInterface;
use webignition\BasilCompilableSource\RenderableInterface;
use webignition\BasilCompilableSource\RenderFromTemplateTrait;
use webignition\BasilCompilableSource\RenderSource;
use webignition\BasilCompilableSource\RenderSourceInterface;

class AssignmentExpression implements AssignmentExpressionInterface, RenderableInterface
{
    use RenderFromTemplateTrait;

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

    public function getRenderSource(): RenderSourceInterface
    {
        return new RenderSource(
            self::RENDER_TEMPLATE,
            [
                'variable' => $this->variable->render(),
                'operator' => $this->operator,
                'value' => $this->value->render(),
            ]
        );
    }
}
