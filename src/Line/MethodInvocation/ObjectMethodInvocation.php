<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Line\MethodInvocation;

use webignition\BasilCompilableSource\Line\ExpressionInterface;
use webignition\BasilCompilableSource\Metadata\MetadataInterface;
use webignition\BasilCompilableSource\ResolvableVariablePlaceholderInterface;
use webignition\BasilCompilableSource\VariablePlaceholderInterface;

class ObjectMethodInvocation extends MethodInvocation implements ObjectMethodInvocationInterface
{
    private const RENDER_PATTERN = '%s->%s';

    private ResolvableVariablePlaceholderInterface $objectPlaceholder;

    /**
     * @param ResolvableVariablePlaceholderInterface $objectPlaceholder
     * @param string $methodName
     * @param ExpressionInterface[] $arguments
     * @param string $argumentFormat
     */
    public function __construct(
        ResolvableVariablePlaceholderInterface $objectPlaceholder,
        string $methodName,
        array $arguments = [],
        string $argumentFormat = self::ARGUMENT_FORMAT_INLINE
    ) {
        parent::__construct($methodName, $arguments, $argumentFormat);

        $this->objectPlaceholder = $objectPlaceholder;
    }

    public function getObjectPlaceholder(): VariablePlaceholderInterface
    {
        return $this->objectPlaceholder;
    }

    public function getMetadata(): MetadataInterface
    {
        return parent::getMetadata()->merge($this->objectPlaceholder->getMetadata());
    }

    public function render(): string
    {
        $objectPlaceholder = $this->getObjectPlaceholder()->render();
        if ($this->suppressErrors === true) {
            $objectPlaceholder = '@' . $objectPlaceholder;
        }

        return sprintf(
            self::RENDER_PATTERN,
            $objectPlaceholder,
            parent::renderWithoutErrorSuppression()
        );
    }
}
