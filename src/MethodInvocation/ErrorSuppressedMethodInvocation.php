<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\MethodInvocation;

use webignition\BasilCompilableSource\Metadata\MetadataInterface;
use webignition\BasilCompilableSource\MethodArguments\MethodArgumentsInterface;

class ErrorSuppressedMethodInvocation implements MethodInvocationInterface
{
    private MethodInvocationInterface $invocation;

    public function __construct(MethodInvocationInterface $invocation)
    {
        $this->invocation = $invocation;
    }

    public function getMetadata(): MetadataInterface
    {
        return $this->invocation->getMetadata();
    }

    public function getCall(): string
    {
        return $this->invocation->getCall();
    }

    public function getArguments(): MethodArgumentsInterface
    {
        return $this->invocation->getArguments();
    }

    public function render(): string
    {
        return '@' . $this->invocation->render();
    }
}
