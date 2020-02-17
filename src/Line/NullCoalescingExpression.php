<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Line;

use webignition\BasilCompilableSource\Metadata\Metadata;

class NullCoalescingExpression extends AbstractExpression
{
    private const RENDER_TEMPLATE = '%s ?? %s';

    private $expression;
    private $default;

    /**
     * @param ExpressionInterface $expression
     * @param ExpressionInterface $default
     * @param string|null $castTo
     */
    public function __construct(ExpressionInterface $expression, ExpressionInterface $default, ?string $castTo = null)
    {
        $this->expression = $expression;
        $this->default = $default;

        $metadata = new Metadata();
        $metadata = $metadata->merge($this->expression->getMetadata());
        $metadata = $metadata->merge($this->default->getMetadata());

        parent::__construct($castTo, $metadata);
    }

    public function getExpression(): ExpressionInterface
    {
        return $this->expression;
    }

    public function getDefault(): ExpressionInterface
    {
        return $this->default;
    }

    public function render(): string
    {
        $cast = parent::render();
        $comparison = sprintf(self::RENDER_TEMPLATE, $this->expression->render(), $this->default->render());

        if (null !== $this->getCastTo()) {
            $comparison = '(' . $comparison . ')';
        }


        return $cast . $comparison;
    }
}
