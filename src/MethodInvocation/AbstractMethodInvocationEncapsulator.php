<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\MethodInvocation;

use webignition\BasilCompilableSource\Metadata\MetadataInterface;
use webignition\BasilCompilableSource\MethodArguments\MethodArgumentsInterface;

abstract class AbstractMethodInvocationEncapsulator implements InvocableInterface
{
    protected MethodInvocation $invocation;

    public function __construct(string $methodName, ?MethodArgumentsInterface $arguments = null)
    {
        $this->invocation = new MethodInvocation($methodName, $arguments);
    }

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

    abstract protected function getAdditionalMetadata(): MetadataInterface;
}
