<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Line;

use webignition\BasilCompilableSource\LineInterface;

class ClassDependency implements LineInterface
{
    private const RENDER_PATTERN = 'use %s;';

    private $className;
    private $alias;

    public function __construct(string $className, ?string $alias = null)
    {
        $this->className = $className;
        $this->alias = $alias;
    }

    public function render(): string
    {
        $content = $this->className;

        if (null !== $this->alias) {
            $content .= ' as ' . $this->alias;
        }

        return sprintf(self::RENDER_PATTERN, $content);
    }
}
