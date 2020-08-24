<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Factory;

abstract class AbstractMethodInvocationFactory
{
    protected ArgumentFactory $argumentFactory;

    public function __construct(ArgumentFactory $argumentFactory)
    {
        $this->argumentFactory = $argumentFactory;
    }
}
