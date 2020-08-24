<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Factory;

use webignition\BasilCompilableSource\MethodInvocation\StaticObjectMethodInvocation;
use webignition\BasilCompilableSource\MethodInvocation\StaticObjectMethodInvocationInterface;
use webignition\BasilCompilableSource\StaticObject;

class StaticObjectMethodInvocationFactory
{
    private ArgumentFactory $argumentFactory;

    public function __construct(ArgumentFactory $argumentFactory)
    {
        $this->argumentFactory = $argumentFactory;
    }

    public static function createFactory(): self
    {
        return new StaticObjectMethodInvocationFactory(
            ArgumentFactory::createFactory()
        );
    }

    /**
     * @param StaticObject $staticObject
     * @param string $methodName
     * @param array<mixed> $arguments
     *
     * @return StaticObjectMethodInvocationInterface
     */
    public function create(
        StaticObject $staticObject,
        string $methodName,
        array $arguments = []
    ): StaticObjectMethodInvocationInterface {
        return new StaticObjectMethodInvocation($staticObject, $methodName, $this->argumentFactory->create($arguments));
    }
}
