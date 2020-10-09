<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\MethodInvocation;

use webignition\BasilCompilableSource\Expression\ExpressionInterface;
use webignition\BasilCompilableSource\Metadata\MetadataInterface;
use webignition\BasilCompilableSource\MethodArguments\MethodArgumentsInterface;

class ObjectMethodInvocation extends AbstractMethodInvocationEncapsulator implements MethodInvocationInterface
{
    private const RENDER_PATTERN = '%s->%s';

    private ExpressionInterface $object;

    public function __construct(
        ExpressionInterface $object,
        string $methodName,
        ?MethodArgumentsInterface $arguments = null
    ) {
        parent::__construct($methodName, $arguments);
        $this->object = $object;
    }

    protected function getAdditionalMetadata(): MetadataInterface
    {
        return $this->object->getMetadata();
    }

    public function render(): string
    {
        return sprintf(
            self::RENDER_PATTERN,
            $this->object->render(),
            $this->invocation->render()
        );
    }
}
