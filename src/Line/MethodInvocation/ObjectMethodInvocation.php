<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Line\MethodInvocation;

use webignition\BasilCompilableSource\Line\ExpressionInterface;
use webignition\BasilCompilableSource\Metadata\MetadataInterface;
use webignition\BasilCompilableSource\VariablePlaceholder;

class ObjectMethodInvocation extends MethodInvocation implements ObjectMethodInvocationInterface
{
    private const RENDER_PATTERN = '%s->%s';

    private $objectPlaceholder;

    /**
     * @param VariablePlaceholder $objectPlaceholder
     * @param string $methodName
     * @param ExpressionInterface[] $arguments
     * @param string $argumentFormat
     * @param string|null $castTo
     */
    public function __construct(
        VariablePlaceholder $objectPlaceholder,
        string $methodName,
        array $arguments = [],
        string $argumentFormat = self::ARGUMENT_FORMAT_INLINE,
        ?string $castTo = null
    ) {
        parent::__construct($methodName, $arguments, $argumentFormat, $castTo);

        $this->objectPlaceholder = $objectPlaceholder;
    }

    public function getObjectPlaceholder(): VariablePlaceholder
    {
        return $this->objectPlaceholder;
    }

    public function getMetadata(): MetadataInterface
    {
        return $this->objectPlaceholder->getMetadata();
    }

    public function render(): string
    {
        return $this->renderCastTo() . sprintf(
            self::RENDER_PATTERN,
            $this->getObjectPlaceholder()->render(),
            parent::renderWithoutCasting()
        );
    }
}
