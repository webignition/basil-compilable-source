<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\MethodInvocation;

use webignition\BasilCompilableSource\Expression\ExpressionInterface;
use webignition\BasilCompilableSource\Metadata\MetadataInterface;
use webignition\BasilCompilableSource\MethodArguments\MethodArgumentsInterface;
use webignition\BasilCompilableSource\RenderSource;
use webignition\BasilCompilableSource\RenderSourceInterface;

class ObjectMethodInvocation extends AbstractMethodInvocationEncapsulator implements MethodInvocationInterface
{
    private const RENDER_TEMPLATE = '{{ object }}->{{ method_invocation }}';

    private ExpressionInterface $object;

    public function __construct(
        ExpressionInterface $object,
        string $methodName,
        ?MethodArgumentsInterface $arguments = null
    ) {
        parent::__construct($methodName, $arguments);
        $this->object = $object;
    }

    public function getRenderSource(): RenderSourceInterface
    {
        return new RenderSource(
            self::RENDER_TEMPLATE,
            [
                'object' => $this->object->render(),
                'method_invocation' => $this->invocation->render(),
            ]
        );
    }

    protected function getAdditionalMetadata(): MetadataInterface
    {
        return $this->object->getMetadata();
    }
}
