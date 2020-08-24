<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Factory;

use webignition\BasilCompilableSource\Expression\ExpressionInterface;
use webignition\BasilCompilableSource\MethodInvocation\ObjectMethodInvocation;

class ObjectMethodInvocationFactory
{
    private ArgumentFactory $argumentFactory;

    public function __construct(ArgumentFactory $argumentFactory)
    {
        $this->argumentFactory = $argumentFactory;
    }

    public static function createFactory(): self
    {
        return new ObjectMethodInvocationFactory(
            ArgumentFactory::createFactory()
        );
    }

    /**
     * @param ExpressionInterface $object
     * @param string $methodName
     * @param array<mixed> $arguments
     *
     * @return ObjectMethodInvocation
     */
    public function create(
        ExpressionInterface $object,
        string $methodName,
        array $arguments = []
    ): ObjectMethodInvocation {
        return new ObjectMethodInvocation($object, $methodName, $this->argumentFactory->create($arguments));
    }
}
