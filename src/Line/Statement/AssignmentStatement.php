<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Line\Statement;

use webignition\BasilCompilableSource\Line\ExpressionInterface;
use webignition\BasilCompilableSource\Metadata\MetadataInterface;
use webignition\BasilCompilableSource\VariablePlaceholder;

class AssignmentStatement extends Statement implements AssignmentStatementInterface
{
    private const RENDER_PATTERN = '%s = %s%s';

    private $placeholder;

    /**
     * @var string|null
     */
    private $castTo = null;

    public function __construct(
        VariablePlaceholder $placeholder,
        ExpressionInterface $expression,
        ?string $castTo = null
    ) {
        parent::__construct($expression);

        $this->placeholder = $placeholder;
        $this->castTo = $castTo;
    }

    public function getVariablePlaceholder(): VariablePlaceholder
    {
        return $this->placeholder;
    }

    public function getCastTo(): ?string
    {
        return $this->castTo;
    }

    public function getMetadata(): MetadataInterface
    {
        return parent::getMetadata()->merge($this->placeholder->getMetadata());
    }

    public function render(): string
    {
        $cast = null === $this->castTo
            ? ''
            : '(' . $this->castTo . ') ';

        return sprintf(
            self::RENDER_PATTERN,
            $this->placeholder->render(),
            $cast,
            parent::render()
        );
    }
}
