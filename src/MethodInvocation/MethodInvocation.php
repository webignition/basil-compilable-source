<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\MethodInvocation;

use webignition\BasilCompilableSource\Metadata\MetadataInterface;
use webignition\BasilCompilableSource\MethodArguments\MethodArguments;
use webignition\BasilCompilableSource\MethodArguments\MethodArgumentsInterface;

class MethodInvocation implements MethodInvocationInterface
{
    public const ARGUMENT_FORMAT_INLINE = 'inline';
    public const ARGUMENT_FORMAT_STACKED = 'stacked';

    private const RENDER_PATTERN = '%s(%s)';

    private string $methodName;
    private MethodArgumentsInterface $arguments;

    /**
     * @var bool
     */
    protected bool $suppressErrors = false;

    /**
     * @param string $methodName
     * @param MethodArgumentsInterface|null $arguments
     */
    public function __construct(string $methodName, ?MethodArgumentsInterface $arguments = null)
    {
        $this->methodName = $methodName;
        $this->arguments = $arguments ?? new MethodArguments([]);
    }

    public function getMethodName(): string
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
        $methodName = $this->getMethodName();
        if ($this->suppressErrors === true) {
            $methodName = '@' . $methodName;
        }

        return $this->renderMethodCall($methodName);
    }

    public function enableErrorSuppression(): void
    {
        $this->suppressErrors = true;
    }

    public function disableErrorSuppression(): void
    {
        $this->suppressErrors = false;
    }

    protected function renderWithoutErrorSuppression(): string
    {
        return $this->renderMethodCall($this->getMethodName());
    }

    private function renderMethodCall(string $methodName): string
    {
        return sprintf(
            self::RENDER_PATTERN,
            $methodName,
            $this->arguments->render()
        );
    }
}
