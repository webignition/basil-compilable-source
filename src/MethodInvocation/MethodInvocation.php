<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\MethodInvocation;

use webignition\BasilCompilableSource\Metadata\MetadataInterface;
use webignition\BasilCompilableSource\MethodArguments\MethodArguments;
use webignition\BasilCompilableSource\MethodArguments\MethodArgumentsInterface;
use webignition\BasilCompilableSource\RenderFromTemplateTrait;

class MethodInvocation implements MethodInvocationInterface
{
    use RenderFromTemplateTrait;

    private const RENDER_PATTERN = '{{ call }}({{ arguments }})';

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

    protected function getRenderTemplate(): string
    {
        return self::RENDER_PATTERN;
    }

    protected function getRenderContext(): array
    {
        return [
            'call' => $this->getCall(),
            'arguments' => $this->arguments->render(),
        ];
    }
}
