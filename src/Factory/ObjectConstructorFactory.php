<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Factory;

use webignition\BasilCompilableSource\ClassName;
use webignition\BasilCompilableSource\MethodInvocation\ObjectConstructor;

class ObjectConstructorFactory extends AbstractMethodInvocationFactory
{
    public static function createFactory(): self
    {
        return new ObjectConstructorFactory(
            ArgumentFactory::createFactory()
        );
    }

    /**
     * @param ClassName $class
     * @param array<mixed> $arguments
     *
     * @return ObjectConstructor
     */
    public function create(ClassName $class, array $arguments = []): ObjectConstructor
    {
        return new ObjectConstructor($class, $this->argumentFactory->create($arguments));
    }
}
