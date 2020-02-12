<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource;

interface DataProviderMethodDefinitionInterface extends MethodDefinitionInterface
{
    /**
     * @return array<mixed>
     */
    public function getData(): array;
}
