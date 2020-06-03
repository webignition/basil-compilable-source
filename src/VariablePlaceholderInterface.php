<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource;

interface VariablePlaceholderInterface extends SourceInterface
{
    public function getName(): string;
}
