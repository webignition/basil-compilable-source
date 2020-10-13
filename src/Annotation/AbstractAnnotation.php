<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Annotation;

use webignition\BasilCompilableSource\RenderFromTemplateTrait;

abstract class AbstractAnnotation implements AnnotationInterface
{
    use RenderFromTemplateTrait;

    private const RENDER_TEMPLATE = '@{{ name }} {{ arguments }}';

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

    protected function getRenderTemplate(): string
    {
        return self::RENDER_TEMPLATE;
    }

    protected function getRenderContext(): array
    {
        return [
            'name' => $this->name,
            'arguments' => implode(' ', $this->arguments)
        ];
    }
}
