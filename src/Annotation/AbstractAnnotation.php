<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Annotation;

use webignition\BasilCompilableSource\RenderableInterface;
use webignition\BasilCompilableSource\RenderFromTemplateTrait;
use webignition\StubbleResolvable\Resolvable;
use webignition\StubbleResolvable\ResolvableInterface;

abstract class AbstractAnnotation implements AnnotationInterface, RenderableInterface
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

    public function getResolvable(): ResolvableInterface
    {
        return new Resolvable(
            self::RENDER_TEMPLATE,
            [
                'name' => $this->name,
                'arguments' => implode(' ', $this->arguments)
            ]
        );
    }
}
