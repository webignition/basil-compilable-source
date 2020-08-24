<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Factory;

use webignition\BasilCompilableSource\MethodInvocation\MethodInvocation;
use webignition\BasilCompilableSource\MethodInvocation\MethodInvocationInterface;

class MethodInvocationFactory
{
    private ArgumentFactory $argumentFactory;

    public function __construct(ArgumentFactory $argumentFactory)
    {
        $this->argumentFactory = $argumentFactory;
    }

    public static function createFactory(): self
    {
        return new MethodInvocationFactory(
            ArgumentFactory::createFactory()
        );
    }

    /**
     * @param string $methodName
     * @param array<mixed> $arguments
     *
     * @return MethodInvocationInterface
     */
    public function create(string $methodName, array $arguments): MethodInvocationInterface
    {
        return new MethodInvocation($methodName, $this->argumentFactory->create($arguments));
    }
}
