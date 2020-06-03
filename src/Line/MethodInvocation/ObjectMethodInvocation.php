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

    private VariablePlaceholderInterface $objectPlaceholder;

    /**
     * @param VariablePlaceholderInterface $objectPlaceholder
     * @param string $methodName
     * @param ExpressionInterface[] $arguments
     * @param string $argumentFormat
     */
    public function __construct(
        VariablePlaceholderInterface $objectPlaceholder,
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
        $metadata = parent::getMetadata();

        if ($this->objectPlaceholder instanceof ResolvableVariablePlaceholderInterface) {
            $metadata = $metadata->merge($this->objectPlaceholder->getMetadata());
        }

        return $metadata;
    }

    public function render(): string
    {
        $objectPlaceholder = $this->objectPlaceholder->render();
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
