<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Line;

use webignition\BasilCompilableSource\LineInterface;

class ClassDependency implements LineInterface
{
    private const RENDER_PATTERN = 'use %s;';
    private const FQCN_PART_DELIMITER = '\\';

    private string $className;
    private ?string $alias;

    public function __construct(string $className, ?string $alias = null)
    {
        $this->className = $className;
        $this->alias = $alias;
    }

    public static function isFullyQualifiedClassName(string $className): bool
    {
        return strtolower($className) !== $className;
    }

    public function getClass(): string
    {
        $classNameParts = explode(self::FQCN_PART_DELIMITER, $this->className);

        return array_pop($classNameParts);
    }

    public function getClassName(): string
    {
        return $this->className;
    }

    public function getAlias(): ?string
    {
        return $this->alias;
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
