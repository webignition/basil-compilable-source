<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource;

class ClassSignature
{
    private const RENDER_TEMPLATE = 'class %s %s';

    private string $name;
    private ?ClassName $baseClass;

    public function __construct(string $name, ?ClassName $baseClass = null)
    {
        $this->name = $name;
        $this->baseClass = $baseClass;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getBaseClass(): ?ClassName
    {
        return $this->baseClass;
    }

    public function render(): string
    {
        $extendsSegment = '';

        if ($this->baseClass instanceof ClassName) {
            $extendsSegment = 'extends ' . $this->baseClass->renderClassName();
        }

        return trim(sprintf(self::RENDER_TEMPLATE, $this->name, $extendsSegment));
    }
}
