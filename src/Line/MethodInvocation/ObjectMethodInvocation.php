<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Line\MethodInvocation;

class ObjectMethodInvocation extends MethodInvocation implements ObjectMethodInvocationInterface
{
    private const RENDER_PATTERN = '%s->%s';

    private $object;

    public function __construct(
        string $object,
        string $methodName,
        array $arguments = [],
        string $argumentFormat = self::ARGUMENT_FORMAT_INLINE
    ) {
        parent::__construct($methodName, $arguments, $argumentFormat);

        $this->object = $object;
    }

    public function getObject(): string
    {
        return $this->object;
    }

    public function render(): string
    {
        return sprintf(
            self::RENDER_PATTERN,
            $this->getObject(),
            parent::render()
        );
    }
}
