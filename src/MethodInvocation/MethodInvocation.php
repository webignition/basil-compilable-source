<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\MethodInvocation;

use webignition\BasilCompilableSource\Metadata\MetadataInterface;
use webignition\BasilCompilableSource\MethodArguments\MethodArguments;
use webignition\BasilCompilableSource\MethodArguments\MethodArgumentsInterface;
use webignition\BasilCompilableSource\RenderableInterface;
use webignition\BasilCompilableSource\RenderFromTemplateTrait;
use webignition\BasilCompilableSource\RenderSource;
use webignition\BasilCompilableSource\RenderSourceInterface;

class MethodInvocation implements MethodInvocationInterface, RenderableInterface
{
    use RenderFromTemplateTrait;

    private const RENDER_TEMPLATE = '{{ call }}({{ arguments }})';

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

    public function getRenderSource(): RenderSourceInterface
    {
        return new RenderSource(
            self::RENDER_TEMPLATE,
            [
                'call' => $this->getCall(),
                'arguments' => $this->arguments->render(),
            ]
        );
    }
}
