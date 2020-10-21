<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Expression;

class ArrayKey
{
    private string $key;

    public function __construct(string $key)
    {
        $this->key = $key;
    }

    public function __toString(): string
    {
        return '\'' . $this->key . '\'';
    }
}
