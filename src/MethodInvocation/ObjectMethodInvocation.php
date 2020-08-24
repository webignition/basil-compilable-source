<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\MethodInvocation;

use webignition\BasilCompilableSource\Expression\ExpressionInterface;
use webignition\BasilCompilableSource\Metadata\MetadataInterface;
use webignition\BasilCompilableSource\VariableDependencyInterface;

class ObjectMethodInvocation extends MethodInvocation
{
    private const RENDER_PATTERN = '%s->%s';

    private ExpressionInterface $object;

    /**
     * @param ExpressionInterface $object
     * @param string $methodName
     * @param ExpressionInterface[] $arguments
     */
    public function __construct(
        ExpressionInterface $object,
        string $methodName,
        array $arguments = []
    ) {
        parent::__construct($methodName, $arguments);

        $this->object = $object;
    }

    public function getMetadata(): MetadataInterface
    {
        $metadata = parent::getMetadata();

        if ($this->object instanceof VariableDependencyInterface) {
            $metadata = $metadata->merge($this->object->getMetadata());
        }

        return $metadata;
    }

    public function render(): string
    {
        $objectPlaceholder = $this->object->render();
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
