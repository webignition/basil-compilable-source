<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Line;

use webignition\BasilCompilableSource\Metadata\Metadata;

class ComparisonExpression extends AbstractExpression
{
    private const RENDER_TEMPLATE = '%s %s %s';

    private $leftHandSide;
    private $rightHandSide;
    private $comparison;

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

    public function render(): string
    {
        return sprintf(
            self::RENDER_TEMPLATE,
            $this->leftHandSide->render(),
            $this->comparison,
            $this->rightHandSide->render()
        );
    }
}
