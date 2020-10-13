<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\MethodInvocation;

use webignition\BasilCompilableSource\Metadata\MetadataInterface;
use webignition\BasilCompilableSource\MethodArguments\MethodArgumentsInterface;
use webignition\BasilCompilableSource\RenderFromTemplateTrait;

abstract class AbstractMethodInvocationEncapsulator implements InvocableInterface
{
    use RenderFromTemplateTrait;

    protected MethodInvocation $invocation;

    public function __construct(string $methodName, ?MethodArgumentsInterface $arguments = null)
    {
        $this->invocation = new MethodInvocation($methodName, $arguments);
    }

    abstract protected function getAdditionalMetadata(): MetadataInterface;

    public function getMetadata(): MetadataInterface
    {
        $metadata = $this->invocation->getMetadata();
        return $metadata->merge($this->getAdditionalMetadata());
    }

    public function getCall(): string
    {
        return $this->invocation->getCall();
    }

    public function getArguments(): MethodArgumentsInterface
    {
        return $this->invocation->getArguments();
    }
}
