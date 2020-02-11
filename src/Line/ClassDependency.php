<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Line;

use webignition\BasilCompilableSource\LineInterface;

class ClassDependency implements LineInterface
{
    private const RENDER_PATTERN = 'use %s;';
    private const FQCN_PART_DELIMITER = '\\';

    private $className;
    private $alias;

    public function __construct(string $className, ?string $alias = null)
    {
        $this->className = $className;
        $this->alias = $alias;
    }

    public function getClass(): string
    {
        if (0 === substr_count($this->className, self::FQCN_PART_DELIMITER)) {
            return $this->className;
        }

        $classNameParts = explode(self::FQCN_PART_DELIMITER, $this->className);

        return array_pop($classNameParts);
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
