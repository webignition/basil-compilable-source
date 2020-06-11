<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Annotation;

abstract class AbstractAnnotation implements AnnotationInterface
{
    private string $name;

    /**
     * @var string[]
     */
    private array $arguments;

    /**
     * @param string $name
     * @param string[] $arguments
     */
    public function __construct(string $name, array $arguments)
    {
        $this->name = $name;
        $this->arguments = $arguments;
    }

    public function render(): string
    {
        return sprintf(
            '@%s %s',
            $this->name,
            implode(' ', $this->arguments)
        );
    }
}
