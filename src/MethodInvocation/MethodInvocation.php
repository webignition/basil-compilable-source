<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\MethodInvocation;

use webignition\BasilCompilableSource\Metadata\MetadataInterface;
use webignition\BasilCompilableSource\MethodArguments\MethodArguments;
use webignition\BasilCompilableSource\MethodArguments\MethodArgumentsInterface;

class MethodInvocation implements MethodInvocationInterface
{
    private const RENDER_PATTERN = '%s(%s)';

    private string $methodName;
    private MethodArgumentsInterface $arguments;

    /**
     * @param string $methodName
     * @param MethodArgumentsInterface|null $arguments
     */
    public function __construct(string $methodName, ?MethodArgumentsInterface $arguments = null)
    {
        $this->methodName = $methodName;
        $this->arguments = $arguments ?? new MethodArguments([]);
    }

    public function getCall(): string
    {
        return $this->methodName;
    }

    public function getArguments(): MethodArgumentsInterface
    {
        return $this->arguments;
    }

    public function getMetadata(): MetadataInterface
    {
        return $this->arguments->getMetadata();
    }

    public function render(): string
    {
        return sprintf(
            self::RENDER_PATTERN,
            $this->getCall(),
            $this->arguments->render()
        );
    }
}
