<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Expression;

use webignition\BasilCompilableSource\Metadata\Metadata;
use webignition\BasilCompilableSource\RenderFromTemplateTrait;

class ComparisonExpression extends AbstractExpression
{
    use RenderFromTemplateTrait;

    private const RENDER_TEMPLATE = '{{ left_hand_side }} {{ comparison}} {{ right_hand_side }}';

    private ExpressionInterface $leftHandSide;
    private ExpressionInterface $rightHandSide;
    private string $comparison;

    /**
     * @param ExpressionInterface $leftHandSide
     * @param ExpressionInterface $rightHandSide
     * @param string $comparison
     */
    public function __construct(
        ExpressionInterface $leftHandSide,
        ExpressionInterface $rightHandSide,
        string $comparison
    ) {
        $this->leftHandSide = $leftHandSide;
        $this->rightHandSide = $rightHandSide;
        $this->comparison = $comparison;

        $metadata = new Metadata();
        $metadata = $metadata->merge($this->leftHandSide->getMetadata());
        $metadata = $metadata->merge($this->rightHandSide->getMetadata());

        parent::__construct($metadata);
    }

    public function getLeftHandSide(): ExpressionInterface
    {
        return $this->leftHandSide;
    }

    public function getRightHandSide(): ExpressionInterface
    {
        return $this->rightHandSide;
    }

    public function getComparison(): string
    {
        return $this->comparison;
    }


    protected function getRenderTemplate(): string
    {
        return self::RENDER_TEMPLATE;
    }

    protected function getRenderContext(): array
    {
        return [
            'left_hand_side' => $this->leftHandSide->render(),
            'comparison' => $this->comparison,
            'right_hand_side' => $this->rightHandSide->render(),
        ];
    }
}
